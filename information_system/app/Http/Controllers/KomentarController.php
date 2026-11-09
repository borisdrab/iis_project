<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Udalost;
use App\Models\Komentar;
use App\Models\Uzivatel;
use Illuminate\Support\Facades\Auth;

class KomentarController extends Controller
{
    public function store(Request $request, int $udalostId)
    {
        $udalost = Udalost::where('stav', 'schvalene')
            ->findOrFail($udalostId);

        $end = new \DateTime($udalost->datum_do . ' ' . $udalost->cas_do);

        $now = new \DateTime();

        if ($now < $end) {
            return back()->withErrors(['komentar' => 'Komentáre je možné pridávať až po skončení udalosti.']);
        }

        $uzivatelId = Auth::id();

        if (!Uzivatel::where('id', $uzivatelId)->exists()) {
            abort(403, 'Používateľ neexistuje.');
        }

        $validatedData = $request->validate([
            'text' => ['nullable', 'string', 'max:1000'],
            'hodnotenie' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        if (empty($validatedData['text']) && empty($validatedData['hodnotenie'])) {
            return back()->withErrors(['text' => 'Zadaj text alebo hodnotenie.']);
        }

        Komentar::create([
            'udalost_id' => $udalost->id,
            'uzivatel_id' => Auth::id(),
            'text' => $validatedData['text'] ?? null,
            'hodnotenie' => $validatedData['hodnotenie'] ?? null,
            'moderoval_id' => null,
        ]);

        return back();
    }
}
