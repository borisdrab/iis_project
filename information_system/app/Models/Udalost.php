<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Udalost extends Model
{
    protected $table = 'Udalost';
    public $timestamps = false;

    protected $fillable = [
        'oznacenie',
        'datum_od',
        'cas_od',
        'datum_do',
        'cas_do',
        'vedenie_sedenia',
        'kapacita',
        'popis',
        'ikona',
        'miesto_id',
        'kategoria_id',
        'zakladatel_id',
        'stav',
        'schvalil_id',
    ];

    public function miesto()
    {
        return $this->belongsTo(Miesto::class, 'miesto_id');
    }

    public function kategoria()
    {
        return $this->belongsTo(Kategorie::class, 'kategoria_id');
    }

    public function vstupne()
    {
        return $this->hasMany(Vstupne::class, 'udalost_id');
    }

    public function foto()
    {
        return $this->hasMany(UdalostFoto::class, 'udalost_id');
    }

    public function komentare() {
        return $this->hasMany(Komentar::class, 'udalost_id');
    }
}
