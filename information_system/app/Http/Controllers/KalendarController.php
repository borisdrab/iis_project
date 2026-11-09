<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registracia;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KalendarController extends Controller
{
    public function index(): Response
    {
        $userId = Auth::id();

        $registracie = Registracia::with(['udalost.miesto', 'udalost.kategoria'])
            ->where('uzivatel_id', $userId)
            ->get();

        return Inertia::render('kalendar/index', [
            'registracie' => $registracie,
        ]);
    }
}
