<?php

namespace App\Http\Controllers;


use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function readAllTransaksi(Request $request)
    {
        try {
            $allTransaksi = Transaksi::all();
            if ($allTransaksi->isEmpty()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Tidak ada data transaksi tersedia',
                    'data' => null
                ], 404);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data transaksi berhasil diambil',
                    'data' => $allTransaksi
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal menjalankan permintaan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    public function getOrderHistory(Request $request)
    {
        try {
            // Mendapatkan user yang sedang login
            $user = $request->user();

            // Mengecek apakah user ditemukan
            if (!$user) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Pengguna tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Mendapatkan riwayat pesanan user beserta nama status pesanannya
            $orderHistory = Transaksi::select('transaksis.*', 'status_pesanans.nama_status')
                ->join('status_pesanans', 'transaksis.status_id', '=', 'status_pesanans.status_id')
                ->where('transaksis.user_id', $user->id)
                ->orderBy('transaksis.id', 'desc')
                ->get();

            // Mengecek apakah ada pesanan
            if ($orderHistory->isEmpty()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Tidak ada riwayat pesanan',
                    'data' => null
                ], 404);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Riwayat pesanan berhasil diambil',
                    'data' => $orderHistory
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal menjalankan permintaan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function index() {
        
        $data = Transaksi::all();
        return response() -> json ([
            'status' => true,
            'message' => 'Data Ditemukan',
            'data' => $data
        ], 200);

    }

    public function createTransaksi(Request $request)
    {
        $status = '';
        $message = '';
        $data = null;
        $status_code = 201;

        try {
            // Validasi request
            $validatedData = $request->validate([
                'alamat_delivery' => 'required|string|max:255',
                'size' => 'required|string|max:10',
                'merk' => 'required|string|max:50',
                'status_id' => 'nullable|exists:status_pesanans,id',
            ]);

            // Dapatkan user yang sedang login
            $user = Auth::user();

            // Buat transaksi baru
            $transaksi = new Transaksi();
            $transaksi->user_id = $user->id;
            $transaksi->tanggal_transaksi = Carbon::now();
            $transaksi->alamat_delivery = $validatedData['alamat_delivery'];
            $transaksi->size = $validatedData['size'];
            $transaksi->merk = $validatedData['merk'];
            $transaksi->status_id = $validatedData['status_id'] ?? 1;
            $transaksi->save();

            $status = 'success';
            $message = 'Transaksi berhasil dibuat';
            $data = $transaksi;
        } catch (\Exception $e) {
            $status = 'failed';
            $message = 'Gagal menjalankan request: ' . $e->getMessage();
            $status_code = 500;
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }
    public function totalTransaksi()
    {
        try {
            $totalTransaksi = Transaksi::count();
            return response()->json([
                'status' => 'success',
                'message' => 'Total transaksi berhasil dihitung',
                'data' => ['total_transaksi' => $totalTransaksi]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal menjalankan permintaan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    // In TransaksiController.php

public function getAllOrders()
{
    try {
        $orders = Transaksi::with(['user', 'status',])->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Data transaksi berhasil diambil',
            'data' => $orders
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Gagal menjalankan permintaan: ' . $e->getMessage(),
            'data' => null
        ], 500);
    }
}

public function updateOrderStatus(Request $request)
{
    try {
        $transaksi = Transaksi::findOrFail($request->input('transaksi_id'));
        $transaksi->status_id = $request->input('status_id');
        $transaksi->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status transaksi berhasil diperbarui'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Gagal menjalankan permintaan: ' . $e->getMessage()
        ], 500);
    }
}

    
}
