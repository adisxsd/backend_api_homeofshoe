<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $status = '';
        $message = '';
        $data = null;
        $api_token = null;
        $status_code = 201;

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 400);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $status = 'failed';
                $message = 'Email tidak terdaftar';
                $status_code = 401;
            } elseif (!Hash::check($request->password, $user->password)) {
                $status = 'failed';
                $message = 'Password salah';
                $status_code = 401;
            } else {
                $token = $user->createToken('api_token')->plainTextToken;
                $user->save();
                $status = 'success';
                $message = 'Login berhasil';
                $data = $user;
                $status_code = 201;
            }
        } catch (\Exception $e) {
            $status = 'failed';
            $message = 'Gagal menjalankan request: ' . $e->getMessage();
            $status_code = 500;
        } finally {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => $data,
                'access_token' => $token,
            ], $status_code);
        }
    }
    public function adminLogin(Request $request)
    {
        $status = '';
        $message = '';
        $data = null;
        $api_token = null;
        $status_code = 201;

        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 400);
            }

            $user = User::where('username', $request->username)->first();

            if (!$user) {
                $status = 'failed';
                $message = 'Username tidak terdaftar';
                $status_code = 401;
            } elseif (!Hash::check($request->password, $user->password)) {
                $status = 'failed';
                $message = 'Password salah';
                $status_code = 401;
            } elseif ($user->role !== 'admin') {
                $status = 'failed';
                $message = 'Akses ditolak. Bukan admin.';
                $status_code = 403;
            } else {
                $token = $user->createToken('api_token')->plainTextToken;
                $user->save();
                $status = 'success';
                $message = 'Login berhasil';
                $data = $user;
                $status_code = 201;
            }
        } catch (\Exception $e) {
            $status = 'failed';
            $message = 'Gagal menjalankan request: ' . $e->getMessage();
            $status_code = 500;
        } finally {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => $data,
                'access_token' => $token,
            ], $status_code);
        }
    }

    public function logout(Request $request)
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

            // Revoke all tokens for the user
            $user->tokens()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout berhasil',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal menjalankan permintaan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    
    public function register(Request $request)
    {
        $status = '';
        $message = '';
        $data = null;
        $status_code = 201;

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'phone_number' => 'nullable|string|max:15',
                'username' => 'required|string|max:255|unique:users',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 400);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'username' => $request->username,
                'role' => 'customer', // Setting default role to 'customer'
            ]);

            $token = $user->createToken('api_token')->plainTextToken;

            $status = 'success';
            $message = 'Registrasi berhasil';
            $data = $user;

        } catch (\Exception $e) {
            $status = 'failed';
            $message = 'Gagal menjalankan request: ' . $e->getMessage();
            $status_code = 500;
        } finally {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => $data,
                'access_token' => $token ?? null,
            ], $status_code);
        }
    }
}
