<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\Moderator;
use App\Models\Osoba;
use App\Models\Uzivatel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $osoby = DB::table('Osoba')
            ->leftJoin('Uzivatel', 'Osoba.id', '=', 'Uzivatel.id')
            ->leftJoin('Moderator', 'Osoba.id', '=', 'Moderator.id')
            ->leftJoin('Administrator', 'Osoba.id', '=', 'Administrator.id')
            ->select(
                'Osoba.id', 'Osoba.meno', 'Osoba.email', 'Osoba.aktivny',
                DB::raw('Uzivatel.id IS NOT NULL AS is_user'),
                DB::raw('Moderator.id IS NOT NULL AS is_moderator'),
                DB::raw('Administrator.id IS NOT NULL AS is_admin')
            )
            ->orderBy('Osoba.id')
            ->get();

        return response()->json($osoby);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'meno' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:Osoba,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'kontakt' => ['nullable', 'string', 'max:255'],
            'vek' => ['nullable', 'integer', 'between:0,110'],
            'pohlavie' => ['nullable', 'string', 'max:20'],
            'rola' => ['required', 'in:Uzivatel,Moderator,Administrator'],
        ]);

        DB::transaction(function () use ($data) {
            $osoba = Osoba::create([
                'meno' => $data['meno'],
                'email' => $data['email'],
                'heslo_hash' => Hash::make($data['password']),
                'kontakt' => $data['kontakt'] ?? null,
                'aktivny' => true,
            ]);

                Uzivatel::create([
                    'id' => $osoba->id,
                    'vek' => $data['vek'] ?? null,
                    'pohlavie' => $data['pohlavie'] ?? null,
                ]);
            if (in_array($data['rola'], ['Moderator', 'Administrator'])) {
                Moderator::create(['id' => $osoba->id]);
            }
            if ($data['rola'] === 'Administrator') {
                Administrator::create(['id' => $osoba->id]);
            }
        });

        return response()->json(['message' => 'Používateľ bol úspešne vytvorený.'], 201);
    }

    public function update(Request $request, $id)
    {
        $osoba = Osoba::findOrFail($id);

        $data = $request->validate([
            'meno' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:Osoba,email,' . $osoba->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'kontakt' => ['nullable', 'string', 'max:255'],
            'vek' => ['nullable', 'integer', 'between:0,110'],
            'pohlavie' => ['nullable', 'string', 'max:20'],
            'rola' => ['required', 'in:Uzivatel,Moderator,Administrator'],
        ]);

        $stara = $this->aktualnaRola($osoba->id);

        if ($osoba->id === Auth::id() && $data['rola'] !== $stara) {
            return back()->withErrors(['rola' => 'Nemôžeš zmeniť vlastnú rolu.']);
        }

        try {
            DB::transaction(function () use ($osoba, $data, $stara) {
                $updateData = [
                    'meno' => $data['meno'],
                    'email' => $data['email'],
                    'kontakt' => $data['kontakt'] ?? null,
                ];
                if (!empty($data['password'])) {
                    $updateData['heslo_hash'] = Hash::make($data['password']);
                }
                $osoba->update($updateData);

                if ($data['rola'] !== $stara) {
                    if ($stara === 'Administrator') {
                        Administrator::where('id', $osoba->id)->delete();
                    }
                    if ($stara !== 'Uzivatel' && $data['rola'] === 'Uzivatel') {
                        Moderator::where('id', $osoba->id)->delete();
                    }

                    if (in_array($data['rola'], ['Moderator', 'Administrator'])) {
                        Moderator::firstOrCreate(['id' => $osoba->id]);
                    }
                    if ($data['rola'] === 'Administrator') {
                        Administrator::firstOrCreate(['id' => $osoba->id]);
                    }
                }
                    Uzivatel::updateOrCreate(
                        ['id' => $osoba->id],
                        ['vek' => $data['vek'] ?? null, 'pohlavie' => $data['pohlavie'] ?? null]
                    );
            });
        } catch (QueryException $e) {
            return back()->withErrors([
                'rola' => 'Rolu nie je možné zmeniť, lebo osoba má priradené udalosti, schválenia alebo registrácie.',
            ]);
        }

        return response()->json(['message' => 'Používateľ bol úspešne upravený.']);
    }

    public function toggleActive($id)
    {
        $osoba = Osoba::findOrFail($id);

        if ($osoba->id === Auth::id()) {
            return response()->json(['message' => 'Nemôžeš deaktivovať sám seba.'], 422);
        }

        $osoba->aktivny = !$osoba->aktivny;
        $osoba->save();

        return response()->json([
            'message' => 'Stav používateľa bol zmenený.',
            'aktivny' => $osoba->aktivny,
        ]);
    }

    private function aktualnaRola(int $id): string
    {
        if (Administrator::where('id', $id)->exists()) {
            return 'Administrator';
        }
        if (Moderator::where('id', $id)->exists()) {
            return 'Moderator';
        }
        return 'Uzivatel';
    }
}