<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function createCustomer(Request $request)
    {
        $status = 'failed';
        $message = 'test';
        $data = null;
        $status_code = 422; // Ganti status code dengan 422 untuk default jika validasi gagal

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'username' => 'required|string|max:255|unique:users,username',
            'role' => 'nullable|exists:users,role'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validatedData = $validator->validated();

            $newCustomer = User::create([
                'name' => $validatedData['name'],
                'phone_number' => $validatedData['phone_number'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'username' => $validatedData['username'],
                'role'=> 'customer'
            ]);

            if ($newCustomer) {
                $message = 'Customer created successfully';
                $status = 'success';
                $status_code = 200;
            } else {
                $message = 'Failed to create customer';
                $status_code = 400;
            }

            $data = $newCustomer;
        } catch (\Exception $e) {
            $message = 'Request failed: ' . $e->getMessage();
            $status_code = 500;
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }
    public function updateCustomer(Request $request)
{
    $user = auth()->user();
    $status = 'failed';
    $message = 'Test';
    $data = null;
    $status_code = 422;

    $validator = \Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'phone_number' => 'required|string|max:15',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:6',
        'username' => 'required|string|max:255|unique:users,username,' . $user->id,
        'role' => 'nullable|exists:users,role'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $validatedData = $validator->validated();
        $user->name = $validatedData['name'];
        $user->phone_number = $validatedData['phone_number'];
        $user->email = $validatedData['email'];
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }
        $user->username = $validatedData['username'];
        $user->role = $validatedData['role'] ?? 'customer';
        $user->save();

        $message = 'Customer updated successfully';
        $status = 'success';
        $status_code = 200;
        $data = $user;
    } catch (\Exception $e) {
        $message = 'Request failed: ' . $e->getMessage();
        $status_code = 500;
    }

    return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ], $status_code);
}

public function getProfile()
{
    $status = '';
    $message = '';
    $data = null;
    $status_code = 200;

    try {
        $user = auth()->user(); // Mendapatkan pengguna yang sedang terotentikasi

        if ($user) {
            // Jika pengguna ditemukan, set data profil
            $data = $user;
            $status = 'success';
            $message = 'Profil pengguna berhasil diambil';
        } else {
            // Jika pengguna tidak ditemukan, atur pesan kesalahan
            $status = 'failed';
            $message = 'Pengguna tidak ditemukan';
            $status_code = 404;
        }
    } catch (\Exception $e) {
        // Jika terjadi kesalahan, tangani pengecualian
        $status = 'error';
        $message = 'Gagal mengambil profil pengguna: ' . $e->getMessage();
        $status_code = 500;
    }

    // Kembalikan respons JSON dengan status, pesan, dan data profil
    return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ], $status_code);
}


    public function readAllCustomers(Request $request)
    {
        $status = '';
        $message = '';
        $data = '';
        $status_code = 200;

        try {
            $allCustomers = User::all();
            if (!is_null($allCustomers) && $allCustomers->isNotEmpty()) {
                $message = 'Data pelanggan berhasil diambil';
                $status_code = 200;
            } else {
                $message = 'Tidak ada data pelanggan yang tersedia';
                $status_code = 404;
            }
            $status = 'success';
            $data = $allCustomers;
        } catch (\Exception $e) {
            $status = 'failed';
            $message = 'Gagal menjalankan permintaan: ' . $e->getMessage();
            $status_code = 500;
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }
    public function totalCustomer()
    {
        try {
            $totalCustomer = User::count();
            return response()->json([
                'status' => 'success',
                'message' => 'Total pelanggan berhasil dihitung',
                'data' => ['total_customer' => $totalCustomer]
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
