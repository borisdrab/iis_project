<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UdalostFoto;
use App\Models\Udalost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UdalostFotoController extends Controller
{
    public function store(Request $request, int $udalostId)
    {
        $udalost = Udalost::findOrFail($udalostId);

        if (Auth::id() !== $udalost->zakladatel_id) {
            abort(403);
        }

        $validatedData = $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max: 5120'],
        ]);

        $path = $validatedData['foto']->store('udalosti', 'public');

        UdalostFoto::create([
            'udalost_id' => $udalost->id,
            'url' => Storage::url($path),
        ]);

        return back();
    }

    public function delete(UdalostFoto $foto)
    {
        $udalost = Udalost::findOrFail($foto->udalost_id);

        if (Auth::id() !== $udalost->zakladatel_id) {
            abort(403);
        }

        $path = str_replace('/storage/', '', $foto->url);
        Storage::disk('public')->delete($path);

        $foto->delete();

        return back()->with('oznamenie', 'Fotografia bola odstránená.');
    }
}
