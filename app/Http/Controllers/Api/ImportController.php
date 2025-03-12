<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        // Validasi file
        $validator = Validator::make($request->all(), [
            'File' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        // Proses import
        try {
            Excel::import(new UsersImport, $request->file('File'));

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diimpor dari Excel.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage()
            ], 500);
        }
    }
}
