<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategorie extends Model
{
    protected $table = 'kategorie';
    public $timestamps = false;
    protected $fillable = [
        'typ',
        'rodic_id',
        'navrhol_id',
        'stav',
        'schvalil_id',
    ];

    public function navrhovatel() {
        return $this->belongsTo(Osoba::class, 'navrhol_id');
    }

    public function rodic() {
        return $this->belongsTo(Kategorie::class, 'rodic_id');
    }
}
