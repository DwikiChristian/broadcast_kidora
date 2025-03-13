<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            ], [
            'file.required' => 'File wajib diunggah.',
            'file.mimes' => 'File harus dalam format PDF.',
            'file.max' => 'Ukuran file tidak boleh lebih dari 10MB.',
        ]);

        $file = $request->file('file');
        $timestamp = now()->timestamp;
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $uniqueFileName = $filename . '_' . $timestamp;

        // Upload ke Cloudinary dengan nama file asli
        $uploadedFile = Cloudinary::upload(
            $file->getRealPath(),
            [          
                'public_id' => $uniqueFileName,
                'format' => 'pdf',
            ]
        );

        return response()->json([
            'url' => $uploadedFile->getSecurePath(),
            'public_id' => $uploadedFile->getPublicId(),
            'original_name' => $file->getClientOriginalName(), 
            'stored_name' => $uniqueFileName . '.pdf', 
        ]);

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
