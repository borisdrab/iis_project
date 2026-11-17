<?php

namespace App\Http\Controllers;

use App\Models\Udalost;
use App\Models\Vstupne;
use App\Models\Zaplatil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Registracia;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RegistraciaController extends Controller 
{
    function registrujNaUdalost(Request $poziadavka, Udalost $udalost)
    {
        if ($udalost->stav !== 'schvalene') {
            throw ValidationException::withMessages([
                'udalost'=> 'Na túto udalosť sa nedá registrovať.',
            ]);
        }

        $zaciatok_udalosti = Carbon::parse($udalost->datum_od . ' ' . $udalost->cas_od, 'Europe/Prague');

        if (Carbon::now('Europe/Prague')->greaterThanOrEqualTo($zaciatok_udalosti)) {
            throw ValidationException::withMessages([
                'udalost' => 'Registrácia na danú udalosť už nie je možná.',
            ]);
        }

        $existuje = Registracia::where('uzivatel_id', Auth::id())
        ->where('udalost_id', $udalost->id)
        ->exists();

        if ($existuje) {
            throw ValidationException::withMessages([
                'udalost' => 'Na danú udalosť ste už zaregistrovaný.',
            ]);
        }

        if ($udalost->kapacita !== null) {
            $pocetRegistracii = Registracia::where('udalost_id', $udalost->id)->count();

            if ($pocetRegistracii >= $udalost->kapacita) {
                throw ValidationException::withMessages([
                    'udalost' => 'Kapacita udalosti je už naplnená.',
                ]);
            }
        }

        $maVstupne = Vstupne::where('udalost_id', $udalost->id)->exists();
        $vybraneVstupne = $poziadavka->input('vstupne_id');

        if ($maVstupne && $vybraneVstupne === null) {
            throw ValidationException::withMessages([
                'vstupne_id' => 'Musíte si vybrať kategóriu vstupného.',
            ]);
        }

        if ($vybraneVstupne !== null) {
            $existujeVstupne = Vstupne::where('id', $vybraneVstupne)
                ->where('udalost_id', $udalost->id)
                ->exists();

            if (!$existujeVstupne) {
                throw ValidationException::withMessages([
                    'vstupne_id' => 'Vybrané vstupné nepatrí danej udalosti.',
                ]);
            }
        }

        Registracia::create([
            'uzivatel_id' => Auth::id(),
            'udalost_id' => $udalost->id,
            'vstupne_id' => $vybraneVstupne,
        ]);

        return back()->with('oznamenie', 'Na udalosť ste sa úspešne zaregistrovali.');
    }

    function zrusRegistraciu(Registracia $registracia)
    {
        if ($registracia->uzivatel_id != Auth::id()) {
            throw ValidationException::withMessages([
                'registracia' => 'Nemáte práva na zrušenie tejto registrácie.'
            ]);
        }

        $registracia->delete();
        return back()->with('oznamenie', 'Registrácia bola zrušená');
    }

    function potvrdPlatbu(Registracia $registracia) {
        $udalost = Udalost::findOrFail($registracia->udalost_id);

        if ($udalost->zakladatel_id !== Auth::id()) {
            abort(403, 'Nedisponujete oprávnením na potvrdenie tejto platby.');
        }

        if ($registracia->vstupne_id === null) {
            abort(422, 'Táto registrácia nemá priradené vstupné.');
        }

        Zaplatil::updateOrCreate(
            ['registracia_id' => $registracia->id],
            ['zaplatene' => true]
        );

        return back()->with('oznamenie', 'Platba bola úspešne potvrdená.');
    }
}