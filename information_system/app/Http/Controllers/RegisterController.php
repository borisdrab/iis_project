<?php

namespace App\Http\Controllers;

use App\Models\Osoba;
use App\Models\Uzivatel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'meno' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:Osoba,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'kontakt' => ['nullable', 'string', 'max:255'],
            'vek' => ['nullable', 'integer', 'between:0,110'],
            'pohlavie' => ['nullable', 'string', 'max:20'],
        ]);

        $osoba = DB::transaction(function () use ($data) {
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

            return $osoba;
        });

        Auth::login($osoba);
        $request->session()->regenerate();

        return redirect('/');
    }
}
