<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registracia extends Model
{
    protected $table = 'registracia';

    public $timestamps = false;

    public function udalost()
    {
        return $this->belongsTo(Udalost::class, 'udalost_id');
    }
}
