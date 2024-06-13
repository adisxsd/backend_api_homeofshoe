<?php

namespace App\Http\Controllers;

use App\Models\StatusPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatusPesananController extends Controller
{
    public function createStatusPesanan(Request $request)
    {
        $status = '';
        $message = '';
        $data = '';
        $status_code = 200;

        $validator = Validator::make($request->all(), [
            'nama_status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validatedData = $validator->validated();

            $newStatusPesanan = StatusPesanan::create([
                'nama_status' => $validatedData['nama_status'],
            ]);

            if ($newStatusPesanan) {
                $message = 'StatusPesanan created successfully';
                $status_code = 200;
            } else {
                $message = 'Failed to create StatusPesanan';
                $status_code = 400;
            }

            $status = 'success';
            $data = $newStatusPesanan;
        } catch (\Exception $e) {
            $status = 'failed';
            $message = 'Request failed: ' . $e->getMessage();
            $status_code = 500;
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }

    public function getStatusPesanan()
    {
        try {
            $statusPesanan = StatusPesanan::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Data status pesanan berhasil diambil',
                'data' => $statusPesanan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal menjalankan permintaan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
