<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    protected $table = 'Komentar';

    public $timestamps = false;

    protected $fillable = [
        'udalost_id',
        'uzivatel_id',
        'text',
        'hodnotenie',
        'moderoval_id',
    ];

    public function udalost()
    {
        return $this->belongsTo(Udalost::class, 'udalost_id');
    }

    public function uzivatel()
    {
        return $this->belongsTo(Uzivatel::class, 'uzivatel_id');
    }

    public function moderator()
    {
        return $this->belongsTo(Moderator::class, 'moderoval_id');
    }
}
