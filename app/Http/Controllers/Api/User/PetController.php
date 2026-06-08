<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;

class PetController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/pets",
     *     tags={"User - Pets"},
     *     summary="Daftar hewan peliharaan milik user",
     *     description="Mengembalikan semua hewan peliharaan yang dimiliki oleh pengguna yang sedang login.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar hewan peliharaan",
     *         @OA\JsonContent(
     *             @OA\Property(property="pets", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Mochi"),
     *                     @OA\Property(property="species", type="string", example="Kucing"),
     *                     @OA\Property(property="breed", type="string", example="Persian"),
     *                     @OA\Property(property="age", type="integer", example=2),
     *                     @OA\Property(property="weight", type="number", format="float", example=4.5),
     *                     @OA\Property(property="gender", type="string", enum={"male","female","unknown"}, example="female"),
     *                     @OA\Property(property="notes", type="string", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid")
     * )
     */
    public function index()
    {
        $pets = auth()->user()->pets;
        return response()->json(['pets' => $pets]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/pets",
     *     tags={"User - Pets"},
     *     summary="Tambah hewan peliharaan",
     *     description="Menambahkan hewan peliharaan baru untuk pengguna yang sedang login.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","species"},
     *             @OA\Property(property="name", type="string", example="Mochi"),
     *             @OA\Property(property="species", type="string", example="Kucing"),
     *             @OA\Property(property="breed", type="string", nullable=true, example="Persian"),
     *             @OA\Property(property="age", type="integer", nullable=true, example=2),
     *             @OA\Property(property="weight", type="number", format="float", nullable=true, example=4.5),
     *             @OA\Property(property="gender", type="string", enum={"male","female","unknown"}, nullable=true, example="female"),
     *             @OA\Property(property="photo", type="string", format="url", nullable=true),
     *             @OA\Property(property="notes", type="string", nullable=true, example="Alergi terhadap makanan tertentu")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Hewan peliharaan berhasil ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Hewan peliharaan berhasil ditambahkan"),
     *             @OA\Property(property="pet", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:100',
            'breed' => 'nullable|string|max:100',
            'age' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'gender' => 'nullable|in:male,female,unknown',
            'photo' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $pet = Pet::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'species' => $request->species,
            'breed' => $request->breed,
            'age' => $request->age,
            'weight' => $request->weight,
            'gender' => $request->gender ?? 'unknown',
            'photo' => $request->photo,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Hewan peliharaan berhasil ditambahkan',
            'pet' => $pet
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/user/pets/{id}",
     *     tags={"User - Pets"},
     *     summary="Update data hewan peliharaan",
     *     description="Memperbarui data hewan peliharaan milik pengguna yang sedang login.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID hewan peliharaan",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Mochi"),
     *             @OA\Property(property="species", type="string", example="Kucing"),
     *             @OA\Property(property="breed", type="string", example="Persian"),
     *             @OA\Property(property="age", type="integer", example=3),
     *             @OA\Property(property="weight", type="number", example=5.0),
     *             @OA\Property(property="gender", type="string", enum={"male","female","unknown"}),
     *             @OA\Property(property="notes", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Data hewan berhasil diperbarui"),
     *     @OA\Response(response=404, description="Hewan peliharaan tidak ditemukan atau bukan milik user")
     * )
     */
    public function update(Request $request, $id)
    {
        $pet = Pet::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'species' => 'sometimes|string|max:100',
            'breed' => 'nullable|string|max:100',
            'age' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'gender' => 'nullable|in:male,female,unknown',
            'photo' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $pet->update($request->all());

        return response()->json([
            'message' => 'Data hewan peliharaan berhasil diperbarui',
            'pet' => $pet
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/user/pets/{id}",
     *     tags={"User - Pets"},
     *     summary="Hapus hewan peliharaan",
     *     description="Menghapus data hewan peliharaan milik pengguna yang sedang login.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID hewan peliharaan",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hewan peliharaan berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Hewan peliharaan berhasil dihapus")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Hewan peliharaan tidak ditemukan")
     * )
     */
    public function destroy($id)
    {
        $pet = Pet::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pet->delete();

        return response()->json(['message' => 'Hewan peliharaan berhasil dihapus']);
    }
}
