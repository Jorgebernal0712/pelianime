<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genero;

class GeneroApiController extends Controller
{
    /** GET /api/generos  (público) */
    public function index()
    {
        return response()->json(Genero::orderBy('nombre')->get(['id', 'nombre']));
    }
}
