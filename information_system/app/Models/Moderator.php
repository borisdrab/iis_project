<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moderator extends Model
{
    public $incrementing = false;
    protected $table = 'moderator';
    public $timestamps = false;
    protected $fillable = ['id'];

}
