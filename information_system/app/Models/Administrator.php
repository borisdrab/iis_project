<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    public $incrementing = false;
    protected $table = 'administrator';
    public $timestamps = false;
    protected $fillable = ['id'];

}
