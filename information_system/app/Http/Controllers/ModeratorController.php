<?php

namespace App\Http\Controllers;

use App\Models\Udalost;
use App\Models\Miesto;
use App\Models\Komentar;
use App\Models\Kategorie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

//use Iluminate\Http\Request;

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
}
