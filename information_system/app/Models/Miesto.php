<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Miesto extends Model
{
    protected $table = 'miesto';
    public $timestamps = false;

    protected $fillable = [
        'adresa',
        'typ',
        'popis',
        'navrhol_id',
        'stav',
        'schvalil_id',
    ];

    public function foto()
    {
        return $this->hasMany(MiestoFoto::class, 'miesto_id');
    }

    public function udalosti()
    {
        return $this->hasMany(Udalost::class, 'miesto_id');
    }
}
