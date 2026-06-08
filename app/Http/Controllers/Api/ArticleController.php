<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/articles",
     *     tags={"Public - Articles"},
     *     summary="Daftar artikel kesehatan hewan",
     *     description="Mengembalikan daftar artikel yang sudah dipublish. Dapat dicari berdasarkan judul, deskripsi, atau tag. Dipaginasi 12 per halaman.",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Kata kunci pencarian (judul/deskripsi/tag)",
     *         @OA\Schema(type="string", example="vaksin")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Nomor halaman",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar artikel berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Cara Merawat Kucing yang Baik"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="image_url", type="string", nullable=true),
     *                     @OA\Property(property="tags", type="string", example="kucing,perawatan"),
     *                     @OA\Property(property="author", type="object")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Article::published()->with('author:id,name');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(12);

        return response()->json($articles);
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     tags={"Public - Articles"},
     *     summary="Detail artikel",
     *     description="Mengembalikan konten lengkap sebuah artikel beserta informasi penulis.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID artikel",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail artikel berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="article", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="image_url", type="string", nullable=true),
     *                 @OA\Property(property="tags", type="string"),
     *                 @OA\Property(property="author", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Artikel tidak ditemukan")
     * )
     */
    public function show($id)
    {
        $article = Article::with('author:id,name,profile_pic')
            ->findOrFail($id);

        return response()->json(['article' => $article]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/articles",
     *     tags={"Admin Panel"},
     *     summary="Buat artikel baru (Admin)",
     *     description="Admin membuat artikel kesehatan hewan baru.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","description","content"},
     *             @OA\Property(property="title", type="string", example="Cara Merawat Anjing yang Baik"),
     *             @OA\Property(property="description", type="string", example="Panduan lengkap merawat anjing peliharaan"),
     *             @OA\Property(property="content", type="string", example="Konten lengkap artikel..."),
     *             @OA\Property(property="image_url", type="string", format="url", nullable=true),
     *             @OA\Property(property="tags", type="string", nullable=true, example="anjing,perawatan,tips")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Artikel berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Artikel berhasil dibuat"),
     *             @OA\Property(property="article", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
            'tags' => 'nullable|string',
        ]);

        $article = Article::create([
            'author_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'image_url' => $request->image_url,
            'tags' => $request->tags,
            'is_published' => true,
        ]);

        return response()->json([
            'message' => 'Artikel berhasil dibuat',
            'article' => $article
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/articles/{id}",
     *     tags={"Admin Panel"},
     *     summary="Update artikel (Admin)",
     *     description="Admin memperbarui konten atau status publish sebuah artikel.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="image_url", type="string", format="url"),
     *             @OA\Property(property="tags", type="string"),
     *             @OA\Property(property="is_published", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Artikel berhasil diperbarui"),
     *     @OA\Response(response=404, description="Artikel tidak ditemukan")
     * )
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'content' => 'sometimes|string',
            'image_url' => 'nullable|url',
            'tags' => 'nullable|string',
            'is_published' => 'sometimes|boolean',
        ]);

        $article->update($request->all());

        return response()->json([
            'message' => 'Artikel berhasil diperbarui',
            'article' => $article
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/articles/{id}",
     *     tags={"Admin Panel"},
     *     summary="Hapus artikel (Admin)",
     *     description="Admin menghapus artikel secara permanen.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Artikel berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Artikel berhasil dihapus")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Artikel tidak ditemukan")
     * )
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return response()->json(['message' => 'Artikel berhasil dihapus']);
    }
}
