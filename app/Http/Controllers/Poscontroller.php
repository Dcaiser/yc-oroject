<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Stockin;

use App\Models\Supplier;
use App\Models\Price;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ActivityController;

class Poscontroller extends Controller
{
        public function index()
    {
        $product = Produk::with('prices')->get();
         $customertypes = ['agent', 'reseller', 'pelanggan'];
        return view('pos.index', compact(['product','customertypes']));


    }

}
