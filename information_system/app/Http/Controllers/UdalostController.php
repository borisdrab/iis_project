<?php

namespace App\Http\Controllers;

use App\Models\Kategorie;
use App\Models\Miesto;
use App\Models\Moderator;
use App\Models\Udalost;
use App\Models\Vstupne;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class UdalostController extends Controller
{
    public function create()
    {
        return Inertia::render('udalosti/create', [
            'miesta' => $this->miesta(),
            'kategorie' => $this->kategorie(),
            'dnes' => now()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->pravidla());
        $this->skontrolujCas($data);
        $jeModerator = $this->jeModerator();

        DB::transaction(function () use ($data, $jeModerator) {
            $udalost = new Udalost(Arr::except($data, ['vstupne']));
            $udalost->zakladatel_id = Auth::id();
            $udalost->stav = $jeModerator ? 'schvalene' : 'cakajuce';
            $udalost->schvalil_id = $jeModerator ? Auth::id() : null;
            $udalost->save();

            foreach ($data['vstupne'] ?? [] as $v) {
                Vstupne::create([
                    'udalost_id' => $udalost->id,
                    'typ' => $v['typ'],
                    'cennik' => $v['cennik'],
                ]);
            }
        });

        return redirect('/')->with('success', 'Udalosť bola uložená.');
    }

    public function edit(Udalost $udalost)
    {
        $this->over($udalost);

        return Inertia::render('udalosti/edit', [
            'udalost' => $udalost,
            'vstupne' => Vstupne::where('udalost_id', $udalost->id)->get(),
            'miesta' => $this->miesta(),
            'kategorie' => $this->kategorie(),
        ]);
    }

    public function update(Request $request, Udalost $udalost)
    {
        $this->over($udalost);

        $data = $request->validate($this->pravidla($udalost->id));
        $this->skontrolujCas($data);

        $pocet = DB::table('Registracia')->where('udalost_id', $udalost->id)->count();
        if (isset($data['kapacita']) && $data['kapacita'] < $pocet) {
            throw ValidationException::withMessages([
                'kapacita' => "Kapacita nemôže byť nižšia ako počet registrácií ($pocet).",
            ]);
        }

        $jeModerator = $this->jeModerator();

        DB::transaction(function () use ($udalost, $data, $jeModerator) {
            $udalost->fill(Arr::except($data, ['vstupne']));
            if (!$jeModerator) {
                $udalost->stav = 'cakajuce';
                $udalost->schvalil_id = null;
            }
            $udalost->save();

            $ponechane = [];
            foreach ($data['vstupne'] ?? [] as $v) {
                $hodnoty = ['typ' => $v['typ'], 'cennik' => $v['cennik']];
                if (!empty($v['id'])) {
                    Vstupne::where('id', $v['id'])
                        ->where('udalost_id', $udalost->id)
                        ->update($hodnoty);
                    $ponechane[] = $v['id'];
                } else {
                    $ponechane[] = Vstupne::create($hodnoty + ['udalost_id' => $udalost->id])->id;
                }
            }

            Vstupne::where('udalost_id', $udalost->id)
                ->whereNotIn('id', $ponechane)
                ->whereNotIn('id', DB::table('Registracia')->whereNotNull('vstupne_id')->select('vstupne_id'))
                ->delete();
        });

        return redirect('/')->with('success', 'Udalosť bola upravená.');
    }

    private function pravidla(?int $ignorujId = null): array
    {
        return [
            'oznacenie' => ['required', 'string', 'max:150', Rule::unique('Udalost', 'oznacenie')->ignore($ignorujId)],
            'datum_od' => ['required', 'date'],
            'cas_od' => ['required', 'date_format:H:i'],
            'datum_do' => ['required', 'date', 'after_or_equal:datum_od'],
            'cas_do' => ['required', 'date_format:H:i'],
            'vedenie_sedenia' => ['nullable', 'string', 'max:255'],
            'kapacita' => ['nullable', 'integer', 'min:1'],
            'popis' => ['nullable', 'string'],
            'ikona' => ['nullable', 'string', 'max:255'],
            'miesto_id' => ['required', Rule::exists('Miesto', 'id')->where('stav', 'schvalene')],
            'kategoria_id' => ['required', Rule::exists('Kategorie', 'id')->where('stav', 'schvalene')],
            'vstupne' => ['nullable', 'array'],
            'vstupne.*.id' => ['nullable', 'integer'],
            'vstupne.*.typ' => ['required', 'string', 'max:100'],
            'vstupne.*.cennik' => ['required', 'numeric', 'min:0'],
        ];
    }

    private function skontrolujCas(array $data): void
    {
        if ($data['datum_od'] === $data['datum_do'] && $data['cas_do'] <= $data['cas_od']) {
            throw ValidationException::withMessages([
                'cas_do' => 'Koniec udalosti musí byť neskôr ako jej začiatok.',
            ]);
        }
    }

    private function jeModerator(): bool
    {
        return Moderator::where('id', Auth::id())->exists();
    }

    private function over(Udalost $udalost): void
    {
        abort_unless((int) $udalost->zakladatel_id === (int) Auth::id() || $this->jeModerator(), 403);
    }

    private function miesta()
    {
        return Miesto::where('stav', 'schvalene')->orderBy('adresa')->get(['id', 'adresa']);
    }

    private function kategorie()
    {
        return Kategorie::where('stav', 'schvalene')->orderBy('typ')->get(['id', 'typ', 'rodic_id']);
    }
}