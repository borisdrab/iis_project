<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Osoba;
use App\Models\Uzivatel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Administrator;
use App\Models\Moderator;
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
            'rola' => ['required', 'in:Uzivatel,Moderator,Administrator']
        ]);

        $osoba = DB::transaction(function () use ($data) {
            $osoba = Osoba::create([
                'meno' => $data['meno'],
                'email' => $data['email'],
                'heslo_hash' => Hash::make($data['password']),
                'kontakt' => $data['kontakt'] ?? null,
                'aktivny' => true,
            ]);
            if($data['rola']=='Uzivatel')
            {
                Uzivatel::create([
                    'id' => $osoba->id,
                    'vek' => $data['vek'] ?? null,
                    'pohlavie' => $data['pohlavie'] ?? null,
                ]);
            }
            if($data['rola']=='Moderator')
            {
                Moderator::create([
                    'id' => $osoba->id,
                ]);
            }
            if($data['rola']=='Administrator')
            {
                Moderator::create([
                    'id' => $osoba->id,
                ]);
                Administrator::create([
                    'id' => $osoba->id,
                ]);
            }

            return response()->json(['message' => 'Používateľ bol úspešne vytvorený.']);
        });

        return redirect('/');
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
            'rola' => ['required', 'in:Uzivatel,Moderator,Administrator']
        ]);

        DB::transaction(function () use ($osoba, $data) {
            $updateData = [
                'meno' => $data['meno'],
                'email' => $data['email'],
                'kontakt' => $data['kontakt'] ?? null,
            ];

            if (!empty($data['password'])) {
                $updateData['heslo_hash'] = Hash::make($data['password']);
            }

            $osoba->update($updateData);

            Uzivatel::where('id', $osoba->id)->delete();
            Moderator::where('id', $osoba->id)->delete();
            Administrator::where('id', $osoba->id)->delete();

            if($data['rola']=='Uzivatel')
            {
                Uzivatel::create([
                    'id' => $osoba->id,
                    'vek' => $data['vek'] ?? null,
                    'pohlavie' => $data['pohlavie'] ?? null,
                ]);
            }
            if($data['rola']=='Moderator')
            {
                Moderator::create([
                    'id' => $osoba->id,
                ]);
            }
            if($data['rola']=='Administrator')
            {
                Moderator::create([
                    'id' => $osoba->id,
                ]);
                Administrator::create([
                    'id' => $osoba->id,
                ]);
            }
        });

        return response()->json(['message' => 'Používateľ bol úspešne upravený.']);
    }

    public function toggleActive($id)
    {
        $osoba = Osoba::findOrFail($id);
        $osoba->aktivny = !$osoba->aktivny;
        $osoba->save();

        return response()->json([
            'message' => 'Stav používateľa bol zmenený.',
            'aktivny' => $osoba->aktivny
        ]);
    }
}
