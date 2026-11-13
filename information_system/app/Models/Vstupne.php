<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vstupne extends Model
{
    protected $table = 'vstupne';

    public $timestamps = false;

    protected $fillable = [
        'cennik',
        'typ',
        'udalost_id',
    ];
}