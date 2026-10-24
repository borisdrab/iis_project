<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategorie extends Model
{
    protected $table = 'Kategorie';
    public $timestamps = false;
    protected $fillable = ['typ','rodic_id'];
}
