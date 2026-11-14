<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
class Osoba extends Authenticatable
{
    protected $table = 'osoba';
    public $timestamps = false;
    protected $fillable = ['meno', 'kontakt', 'email', 'heslo_hash', 'aktivny'];
    protected $hidden = ['heslo_hash'];
    protected $appends = ['name'];

    public function getAuthPasswordName()
    {
        return 'heslo_hash';
    }

    public function getNameAttribute()
    {
        return $this->meno;
    }

    public function getRememberTokenName()
    {
        return '';
    }
}
