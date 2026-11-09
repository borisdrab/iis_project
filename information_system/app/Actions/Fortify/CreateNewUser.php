<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Osoba;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function create(array $input): Osoba
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            $osoba = Osoba::create([
                'meno' => $input['name'],
                'kontakt' => '',
                'email' => $input['email'],
                'heslo_hash' => Hash::make($input['password']),
                'aktivny' => true,
            ]);

            DB::table('Uzivatel')->insert([
                'id' => $osoba->id,
            ]);

            return $osoba;
        });
    }
}