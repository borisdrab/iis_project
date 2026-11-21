<?php

namespace App\Http\Controllers;

use App\Models\Kategorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
class KategorieController extends Controller
{
    function navrhniKategoriu(Request $poziadavka) {
        $udaje = $poziadavka->validate([
            'typ' => 'required|string',
            'rodic_id' => ['nullable', 'integer', Rule::exists('kategorie', 'id')->where('stav', 'schvalene')],
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
