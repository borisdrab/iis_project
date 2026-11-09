<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Miesto;

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
}
