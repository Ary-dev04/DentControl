<?php

namespace App\Http\Controllers\Asistente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GestionAppController extends Controller
{
    public function index()
    {
        return view('asistente.ap.gestion-app');
    }
}