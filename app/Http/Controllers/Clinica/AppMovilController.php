<?php

namespace App\Http\Controllers\Clinica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppMovilController extends Controller
{
    public function acceso()
    {
        return view('asistente.app.acceso');
    }
}