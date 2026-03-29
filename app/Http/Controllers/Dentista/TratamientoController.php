<?php

namespace App\Http\Controllers\Dentista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TratamientoController extends Controller
{
    public function index()
    {
        return view('dentista.tratamientos.tratamientos');
    }
}