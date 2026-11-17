<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zaplatil extends Model
{
    protected $table = 'zaplatil';
    public $timestamps = false;
    protected $fillable = [
        'registracia_id',
        'zaplatene',
    ];
}