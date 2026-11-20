<?php

namespace App\Http\Controllers;

use App\Models\Registracia;
use App\Models\Udalost;
use App\Models\Vstupne;
use App\Models\Zaplatil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistraciaController extends Controller
{
    public function registrujNaUdalost(Request $poziadavka, Udalost $udalost)
    {
        $validovane = $poziadavka->validate([
            'vstupne_id' => ['nullable', 'integer'],
        ]);

        $vybraneVstupne = $validovane['vstupne_id'] ?? null;

        DB::transaction(function () use ($udalost, $vybraneVstupne) {
            $zamknutaUdalost = Udalost::where('id', $udalost->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($zamknutaUdalost->stav !== 'schvalene') {
                throw ValidationException::withMessages([
                    'udalost' => 'Na túto udalosť sa nedá registrovať.',
                ]);
            }

            $zaciatokUdalosti = Carbon::parse(
                $zamknutaUdalost->datum_od . ' ' . $zamknutaUdalost->cas_od,
                'Europe/Prague'
            );

            if (Carbon::now('Europe/Prague')->greaterThanOrEqualTo($zaciatokUdalosti)) {
                throw ValidationException::withMessages([
                    'udalost' => 'Registrácia na danú udalosť už nie je možná.',
                ]);
            }

            $existuje = Registracia::where('uzivatel_id', Auth::id())
                ->where('udalost_id', $zamknutaUdalost->id)
                ->exists();

            if ($existuje) {
                throw ValidationException::withMessages([
                    'udalost' => 'Na danú udalosť ste už zaregistrovaný.',
                ]);
            }

            if ($zamknutaUdalost->kapacita !== null) {
                $pocetRegistracii = Registracia::where('udalost_id', $zamknutaUdalost->id)->count();

                if ($pocetRegistracii >= $zamknutaUdalost->kapacita) {
                    throw ValidationException::withMessages([
                        'udalost' => 'Kapacita udalosti je už naplnená.',
                    ]);
                }
            }

            $maVstupne = Vstupne::where('udalost_id', $zamknutaUdalost->id)->exists();

            if ($maVstupne && $vybraneVstupne === null) {
                throw ValidationException::withMessages([
                    'vstupne_id' => 'Musíte si vybrať kategóriu vstupného.',
                ]);
            }

            if ($vybraneVstupne !== null) {
                $existujeVstupne = Vstupne::where('id', $vybraneVstupne)
                    ->where('udalost_id', $zamknutaUdalost->id)
                    ->exists();

                if (!$existujeVstupne) {
                    throw ValidationException::withMessages([
                        'vstupne_id' => 'Vybrané vstupné nepatrí danej udalosti.',
                    ]);
                }
            }

            Registracia::create([
                'uzivatel_id' => Auth::id(),
                'udalost_id' => $zamknutaUdalost->id,
                'vstupne_id' => $vybraneVstupne,
            ]);
        });

        return back()->with('oznamenie', 'Na udalosť ste sa úspešne zaregistrovali.');
    }

    public function zrusRegistraciu(Registracia $registracia)
    {
        if ((int) $registracia->uzivatel_id !== (int) Auth::id()) {
            throw ValidationException::withMessages([
                'registracia' => 'Nemáte práva na zrušenie tejto registrácie.',
            ]);
        }

        $udalost = Udalost::findOrFail($registracia->udalost_id);

        $zaciatokUdalosti = Carbon::parse(
            $udalost->datum_od . ' ' . $udalost->cas_od,
            'Europe/Prague'
        );

        if (Carbon::now('Europe/Prague')->greaterThanOrEqualTo($zaciatokUdalosti)) {
            throw ValidationException::withMessages([
                'registracia' => 'Registráciu na danú udalosť už nie je možné zrušiť, z dôvodu začatia akcie.',
            ]);
        }

        $registracia->delete();

        return back()->with('oznamenie', 'Registrácia bola úspešne zrušená.');
    }

    public function potvrdPlatbu(Registracia $registracia)
    {
        $udalost = Udalost::findOrFail($registracia->udalost_id);

        if ((int) $udalost->zakladatel_id !== (int) Auth::id()) {
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