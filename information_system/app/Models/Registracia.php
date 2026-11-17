<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registracia extends Model
{
    protected $table = 'registracia';

    public $timestamps = false;

    protected $fillable = [
        'uzivatel_id',
        'udalost_id',
        'vstupne_id',
    ];

    public function udalost()
    {
        return $this->belongsTo(Udalost::class, 'udalost_id');
    }
}
