<?php

namespace App\Http\Controllers;


use App\Models\PaketCuci;
use Illuminate\Http\Request;

class PaketCuciController extends Controller
{
    public function index() {
        
        $data = PaketCuci::all();
        return response() -> json ([
            'status' => true,
            'message' => 'Data Ditemukan',
            'data' => $data
        ], 200);

    }
}
