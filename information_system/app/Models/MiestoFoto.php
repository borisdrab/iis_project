<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiestoFoto extends Model
{
    protected $table = 'MiestoFoto';

    public $timestamps = false;

    protected $fillable = [
        'miesto_id',
        'url',
    ];
}
