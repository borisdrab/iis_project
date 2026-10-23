<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uzivatel extends Model
{
    public $incrementing = false;
    protected $table = 'Uzivatel';
    public $timestamps = false;
    protected $fillable = ['vek','pohlavie','id'];

}
