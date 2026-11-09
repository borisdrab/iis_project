<?php

namespace App\Http\Controllers;

use App\Models\Kategorie;
use App\Models\Udalost;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class UdalostController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search', '');

        $kategoriaId = $request->input('kategoria');

        $kategorie = Kategorie::where('stav', 'schvalene')
            ->orderBy('typ')
            ->get();

        $query = Udalost::with(['miesto', 'kategoria'])
            ->where('stav', 'schvalene');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('oznacenie', 'LIKE', '%' . $search . '%')
                ->orWhere('popis', 'LIKE', '%' . $search . '%');
            });
        }

        if ($kategoriaId) {
            $ids = $this->getCategoryAndChildren(
                (int) $kategoriaId,
                $kategorie
            );

            $query->whereIn('kategoria_id', $ids);
        }

        $udalosti = $query
            ->orderBy('datum_od')
            ->orderBy('cas_od')
            ->get();

        return Inertia::render('udalosti/index', [
            'udalosti' => $udalosti,
            'kategorie' => $kategorie,
            'filters' => [
                'search' => $search,
                'kategoria' => $kategoriaId,
            ],
        ]);
    }

    public function show(int $id): Response
    {
        $udalost = Udalost::with(['miesto', 'kategoria', 'vstupne', 'foto', 'komentare.uzivatel.osoba'])
            ->where('stav', 'schvalene')
            ->findOrFail($id);

        return Inertia::render('udalosti/show', [
            'udalost' => $udalost,
        ]);
    }

    private function getCategoryAndChildren(int $id, $kategorie): array
    {
        $ids = [$id];

        foreach ($kategorie->where('rodic_id', $id) as $child) {
            $ids = array_merge(
                $ids,
                $this->getCategoryAndChildren($child->id, $kategorie)
            );
        }

        return $ids;
    }
}
