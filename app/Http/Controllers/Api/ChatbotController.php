<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/chatbot/ask",
     *     tags={"User - Chatbot"},
     *     summary="Tanya AI Chatbot VETRA",
     *     description="Mengirim pertanyaan ke AI Chatbot berbasis Google Gemini. Chatbot dikonfigurasi sebagai asisten kesehatan hewan peliharaan yang menjawab dalam Bahasa Indonesia. Mendukung parameter 'question' atau 'message'.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="question", type="string", maxLength=1000, nullable=true, example="Apakah kucing saya perlu divaksin setiap tahun?"),
     *             @OA\Property(property="message", type="string", maxLength=1000, nullable=true, description="Alternatif dari 'question'. Gunakan salah satu.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Jawaban dari AI Chatbot",
     *         @OA\JsonContent(
     *             @OA\Property(property="answer", type="string", example="Ya, vaksinasi tahunan sangat penting untuk kucing Anda. Vaksin membantu melindungi kucing dari berbagai penyakit berbahaya seperti...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Pertanyaan tidak boleh kosong",
     *         @OA\JsonContent(
     *             @OA\Property(property="answer", type="string", example="Pertanyaan tidak boleh kosong")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Layanan chatbot tidak tersedia sementara",
     *         @OA\JsonContent(
     *             @OA\Property(property="answer", type="string", example="Maaf, layanan chatbot sedang tidak tersedia.")
     *         )
     *     )
     * )
     */
    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'nullable|string|max:1000',
            'message' => 'nullable|string|max:1000',
        ]);
        
        // Support both 'question' and 'message' parameter
        $userQuestion = $request->question ?? $request->message;
        
        if (!$userQuestion) {
            return response()->json(['answer' => 'Pertanyaan tidak boleh kosong'], 400);
        }
        
        $systemPrompt = "Anda adalah VETRA AI, asisten kesehatan hewan peliharaan yang ramah dan profesional. " .
            "Bantu pengguna dengan pertanyaan seputar kesehatan anjing, kucing, dan hewan peliharaan lainnya. " .
            "Jawab dalam Bahasa Indonesia dengan gaya yang ramah dan mudah dipahami. " .
            "Jika kondisi hewan terlihat serius, selalu sarankan untuk segera berkonsultasi dengan dokter hewan profesional. " .
            "Berikan jawaban yang informatif dan praktis.";
        
        try {
            // Check if Gemini API key is configured
            $apiKey = env('GEMINI_API_KEY');
            
            \Log::info('Chatbot request received', [
                'question' => $userQuestion,
                'api_key_exists' => !empty($apiKey),
                'api_key_length' => strlen($apiKey ?? '')
            ]);
            
            if (!$apiKey) {
                \Log::warning('Gemini API key not configured');
                // Fallback response if no API key
                return response()->json([
                    'answer' => "Terima kasih atas pertanyaan Anda tentang: \"{$userQuestion}\"\n\n" .
                        "Saat ini layanan AI chatbot sedang dalam pengembangan. Untuk konsultasi kesehatan hewan peliharaan, " .
                        "silakan gunakan fitur konsultasi dengan dokter hewan kami atau hubungi klinik terdekat.\n\n" .
                        "Beberapa tips umum:\n" .
                        "• Pastikan hewan mendapat makanan bergizi\n" .
                        "• Lakukan vaksinasi rutin\n" .
                        "• Jaga kebersihan hewan dan lingkungannya\n" .
                        "• Segera konsultasi ke dokter jika ada gejala tidak normal"
                ]);
            }
            
            $model = env('GEMINI_MODEL', 'gemini-2.5-flash');
            
            $response = Http::withoutVerifying()->timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent?key=" . $apiKey, [
                'contents' => [
                    ['parts' => [['text' => $systemPrompt . "\n\nPertanyaan: " . $userQuestion]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024,
                ]
            ]);
            
            \Log::info('Gemini API response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => substr($response->body(), 0, 500) // First 500 chars
            ]);
            
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Check for error in response
                if (isset($responseData['error'])) {
                    \Log::error('Gemini API returned error', $responseData['error']);
                    
                    // Return error details for debugging
                    return response()->json([
                        'answer' => "Error dari Gemini API:\n" .
                            "Code: " . ($responseData['error']['code'] ?? 'N/A') . "\n" .
                            "Message: " . ($responseData['error']['message'] ?? 'N/A') . "\n" .
                            "Status: " . ($responseData['error']['status'] ?? 'N/A')
                    ]);
                }
                
                $reply = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;
                
                if ($reply) {
                    \Log::info('Chatbot response sent successfully');
                    return response()->json(['answer' => $reply]);
                } else {
                    \Log::warning('No reply in Gemini response', ['response' => $responseData]);
                    return response()->json([
                        'answer' => 'Format respons tidak sesuai. Debug: ' . json_encode($responseData)
                    ]);
                }
            } else {
                \Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                // Return actual error for debugging
                return response()->json([
                    'answer' => "Gemini API Error (HTTP " . $response->status() . "):\n" . $response->body()
                ]);
            }
            
            // Fallback response
            return response()->json([
                'answer' => 'Maaf, saya tidak dapat memproses pertanyaan Anda saat ini. ' .
                    'Silakan coba lagi atau hubungi dokter hewan kami untuk konsultasi langsung.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Chatbot error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'answer' => 'Maaf, layanan chatbot sedang tidak tersedia. ' .
                    'Silakan gunakan fitur konsultasi dengan dokter hewan kami atau coba beberapa saat lagi.'
            ], 500);
        }
    }
}
