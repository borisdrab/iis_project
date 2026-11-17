<?php

namespace App\Http\Controllers;

use App\Models\Kategorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategorieController extends Controller
{
    function navrhniKategoriu(Request $poziadavka) {
        $udaje = $poziadavka->validate([
            'typ' => 'required|string',
            'rodic_id' => 'nullable|integer|exists:kategorie,id',
        ]);

        Kategorie::create([
            'typ' => $udaje['typ'],
            'rodic_id' => $udaje['rodic_id'] ?? null,
            'navrhol_id' => Auth::id(),
            'stav' => 'cakajuce',
        ]);

        return back()->with('oznamenie', 'Návrh kategórie bol odoslaný na schválenie.');
    }
}
