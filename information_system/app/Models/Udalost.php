<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Udalost extends Model
{
    protected $table = 'Udalost';
    public $timestamps = false;
    protected $fillable = ['oznacenie','datum_od','cas_od','datum_do','cas_do','vedenie_sedenia','kapacita','popis','ikona','miesto_id','kategoria_id'];

}
