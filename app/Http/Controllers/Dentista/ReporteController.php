<?php

namespace App\Http\Controllers\Dentista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        // SOLO DISEÑO (sin lógica)
        return view('dentista.reportes.index');
    }
}