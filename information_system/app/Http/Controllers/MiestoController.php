<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Miesto;
use Illuminate\Support\Facades\Auth;

class MiestoController extends Controller
{
    public function show(int $id): Response
    {
        $miesto = Miesto::with([
            'foto',
            'udalosti' => function ($query) {
                $query
                    ->where('stav', 'schvalene')
                    ->orderBy('datum_od')
                    ->orderBy('cas_od');
            }
        ])
            ->where('stav', 'schvalene')
            ->findOrFail($id);
        return Inertia::render('miesta/show', ['miesto' => $miesto ]);
    }

    function navrhniMiesto(Request $poziadavka)
    {
        $data = $poziadavka->validate([
            'adresa' => 'required|string',
            'typ' => 'required|string',
            'popis' => 'nullable|string',
        ]);

        if(isset($data['popis'])) {
            $popis = $data['popis'];
        } else {
            $popis = null;
        }

        Miesto::create([
            'adresa' => $data['adresa'],
            'typ' => $data['typ'],
            'popis' => $popis,
            'navrhol_id' => Auth::id(),
            'stav' => 'cakajuce',
        ]);

        return back()->with('oznamenie', 'Miesto bolo navrhnuté');
    }
}
