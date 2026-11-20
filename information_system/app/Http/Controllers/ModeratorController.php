<?php

namespace App\Http\Controllers;

use App\Models\Udalost;
use App\Models\Miesto;
use App\Models\Komentar;
use App\Models\Kategorie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModeratorController extends Controller
{
    function schvalUdalost(Udalost $udalost) {

        if ($udalost->stav !== 'cakajuce') {
            throw ValidationException::withMessages([
                'stav' => 'Udalosť nečaká na schválenie.',
            ]);
        }

        $udalost->stav = 'schvalene';
        $udalost->schvalil_id = Auth::id();
        $udalost->save();

        return back()->with('oznamenie', 'Udalosť bola schvalená.');
    }

    function zamietnutUdalost(Udalost $udalost) {

        if ($udalost->stav !== 'cakajuce') {
            throw ValidationException::withMessages([
                'stav' => 'Udalosť nečaká na zamietnutie.',
            ]);
        }

        $udalost->stav = 'zamietnute';
        $udalost->save();

        return back()->with('oznamenie', 'Udalosť bola zamietnutá.');
    }

    function cakajuceUdalosti() 
    {
        return Udalost::where('stav', 'cakajuce')->get();
    }

    function upravMiesto(Request $poziadavka, Miesto $miesto)
    {
        $data = $poziadavka->validate([
            'adresa' => 'required|string|max:255',
            'typ' => 'nullable|string|max:100',
            'popis' => 'nullable|string',
        ]);

        $miesto->update($data);

        return back()->with('oznamenie', 'Miesto bolo upravené úspešne.');
    }

    function vymazMiesto(Miesto $miesto) 
    {
        if ($miesto->udalosti()->exists()) {
            throw ValidationException::withMessages([
                'miesto' => 'Miesto sa nedá odstrániť, z dôvodu výskytu v existujúcej udalosti.',
            ]);
        }

        $miesto->delete();

        return back()->with('oznamenie', 'Miesto bolo úspešne odstránené.');
    }


    function schvalMiesto(Miesto $miesto) {

        if ($miesto->stav !== 'cakajuce') {
            throw ValidationException::withMessages([
                'stav' => 'Miesto nečaká na schvalenie.',
            ]);
        }

        $miesto->stav = 'schvalene';
        $miesto->schvalil_id = Auth::id();
        $miesto->save();

        return back()->with('oznamenie', 'Miesto bolo schvalené.');
    }

    function zamietnutMiesto(Miesto $miesto) {

        if ($miesto->stav !== 'cakajuce') {
            throw ValidationException::withMessages([
                'stav' => 'Miesto nečaká na zamietnutie.',
            ]);
        }

        $miesto->stav = 'zamietnute';
        $miesto->save();

        return back()->with('oznamenie', 'Miesto bolo zamietnuté.');
    }

    function cakajuceMiesta() 
    {
        return Miesto::where('stav', 'cakajuce')->get();
    }

    function upravKategoriu(Request $poziadavka, Kategorie $kategoria)
    {
        $data = $poziadavka->validate([
            'typ' => 'required|string|max:100',
        ]);

        $kategoria->update($data);

        return back()->with('oznamenie', 'Kategoria bola úspešne upravená.');
    }

    function vymazKategoriu(Kategorie $kategoria)
    {
        if ($kategoria->udalosti()->exists()) {
            throw ValidationException::withMessages([
                'kategoria' => 'Kategóriu sa nedá odstrániť, z dôvodu výskytu v existujúcej udalosti.'
            ]);
        }

        if ($kategoria->deti()->exists()) {
            throw ValidationException::withMessages([
                'kategoria' => 'Kategóriu sa nedá odstrániť, pretože obsahuje podkategórie.'
            ]);
        }

        $kategoria->delete();

        return back()->with('oznamenie', 'Kategória bola úspešne odstránená.');
    }

    function schvalKategoriu(Kategorie $kategoria) {

        if ($kategoria->stav !== 'cakajuce') {
            throw ValidationException::withMessages([
                'stav' => 'Kategória nečaká na schválenie.',
            ]);
        }

        $kategoria->stav = 'schvalene';
        $kategoria->schvalil_id = Auth::id();
        $kategoria->save();

        return back()->with('oznamenie', 'Kategória bola schvalená.');
    }

    function zamietnutKategoriu(Kategorie $kategoria) {

        if ($kategoria->stav !== 'cakajuce') {
            throw ValidationException::withMessages([
                'stav' => 'Kategória nečaká na zamietnutie.',
            ]);
        }

        $kategoria->stav = 'zamietnute';
        $kategoria->save();

        return back()->with('oznamenie', 'Kategória bola zamietnutá.');
    }

    function cakajuceKategorie() 
    {
        return Kategorie::where('stav', 'cakajuce')->get();
    }

    //dorobit komentare

    function odstranKomentar(Komentar $komentar) {

        $komentar->delete();
        return back()->with('oznamenie', 'Komentár bol odstránený.');
    }

    public function index() {
        $udalosti = Udalost::with(['miesto', 'kategoria', 'zakladatel' ])
            ->where('stav', 'cakajuce')
            ->get();

        $miesta = Miesto::with('navrhovatel')
            ->where('stav', 'cakajuce')
            ->get();
        
        $schvaleneMiesta = Miesto::where('stav', 'schvalene')
            ->orderBy('adresa')
            ->get();

        $kategorie = Kategorie::with(['navrhovatel', 'rodic' ])
            ->where('stav', 'cakajuce')
            ->get();

        $schvaleneKategorie = Kategorie::with('rodic')
            ->where('stav', 'schvalene')
            ->orderBy('typ')
            ->get();

        return Inertia::render('moderator/moderation', [
            'udalosti' => $udalosti,
            'miesta' => $miesta,
            'schvaleneMiesta' => $schvaleneMiesta,
            'kategorie' => $kategorie,
            'schvaleneKategorie' => $schvaleneKategorie,
        ]);
    }
}
