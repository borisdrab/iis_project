<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UdalostFoto extends Model
{
    protected $table = 'udalostfoto';

    public $timestamps = false;

    protected $fillable = [
        'udalost_id',
        'url',
    ];
};
