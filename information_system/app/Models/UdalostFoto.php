<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UdalostFoto extends Model
{
    protected $table = 'UdalostFoto';

    public $timestamps = false;

    protected $fillable = [
        'udalost_id',
        'url',
    ];
};
