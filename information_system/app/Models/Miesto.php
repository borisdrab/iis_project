<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Miesto extends Model
{
    protected $table = 'Miesto';
    public $timestamps = false;
    protected $fillable = ['adresa','typ','popis'];
}

