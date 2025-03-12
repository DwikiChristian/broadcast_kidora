<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
    

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
   
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'nip' => 'required|string|max:20|unique:users,nip', // Validasi unik untuk nip
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        // Menambahkan pengguna baru ke database
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'nip' => $request->nip,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input data
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone_number' => 'sometimes|required|string|max:15',
            'nip' => 'sometimes|required|string|max:20|unique:users,nip,' . $id, // Validasi nip unik, kecuali untuk ID ini
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }
    
        // Mencari pengguna berdasarkan ID
        $user = User::find($id);
    
        // Jika pengguna tidak ditemukan
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    
        // Mengupdate hanya data yang ada dalam request
        if ($request->has('name')) {
            $user->name = $request->name;
        }
    
        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }
    
        if ($request->has('nip')) {
            $user->nip = $request->nip;
        }
    
        $user->save(); // Simpan perubahan
    
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Mencari pengguna berdasarkan ID
        $user = User::find($id);

        // Jika pengguna tidak ditemukan
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // Menghapus pengguna
        $user->delete();

        // Mengembalikan response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ], 200);
    }
    public function showAll()
    {
        $users = User::all();  // Mengambil semua data tanpa filter

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function search(Request $request)
    {
        // Ambil parameter pencarian dari request (name, nip, phone_number)
        $query = User::query();

        // Filter berdasarkan nama
        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter berdasarkan nip
        if ($request->has('nip') && $request->nip != '') {
            $query->where('nip', 'like', '%' . $request->nip . '%');
        }

        // Filter berdasarkan phone_number
        if ($request->has('phone_number') && $request->phone_number != '') {
            $query->where('phone_number', 'like', '%' . $request->phone_number . '%');
        }

        // Ambil hasil filter dan urutkan berdasarkan nama A-Z
        $users = $query->orderBy('name', 'asc')->get();

        // Cek jika tidak ada data yang ditemukan
        if ($users->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No users found matching the search criteria.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }


}
    

