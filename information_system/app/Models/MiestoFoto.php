<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiestoFoto extends Model
{
    protected $table = 'miestofoto';

    public $timestamps = false;

    protected $fillable = [
        'miesto_id',
        'url',
    ];
}
