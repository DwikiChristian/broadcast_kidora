<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CloudinaryController extends Controller
{
    public function upload(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'files' => 'required|array|max:5', // Maksimal 5 file
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf,docx|max:2048', // Format yang diperbolehkan
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            // Ambil nama file asli (misalnya: 1234567890_document.pdf)
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // Pisahkan NIP dari nama file (asumsi NIP ada di awal nama file)
            $parts = explode('_', $originalFilename);
            $nip = $parts[0] ?? null;

            if (!$nip) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'NIP tidak ditemukan dalam nama file'
                ], 400);
            }

            // Cari user berdasarkan NIP
            $user = User::where('nip', $nip)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => "User dengan NIP $nip tidak ditemukan"
                ], 404);
            }

            // Upload ke Cloudinary
            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'documents',
                'public_id' => $nip . '_' . uniqid()
            ]);

            // Simpan ke database
            $document = Document::create([
                'user_id' => $user->id,
                'filename' => $uploadedFile->getPublicId(),
                'file_url' => $uploadedFile->getSecurePath()
            ]);

            $uploadedFiles[] = [
                'filename' => $document->filename,
                'file_url' => $document->file_url
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Files uploaded successfully',
            'files' => $uploadedFiles
        ], 200);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'public_id' => 'required|string',
        ]);

        Cloudinary::destroy($request->public_id);

        return response()->json(['message' => 'File deleted successfully']);
    }
    
}
