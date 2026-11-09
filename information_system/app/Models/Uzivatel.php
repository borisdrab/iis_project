<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uzivatel extends Model
{
    protected $table = 'Uzivatel';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'vek',
        'pohlavie',
    ];

    public function osoba()
    {
        return $this->belongsTo(Osoba::class, 'id');
    }
}