<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tome extends Model
{
    use HasFactory;

    protected $fillable = [
        'manga_id',
        'numero',
        'possede',
        'date_achat'
    ];

    protected $casts = [
        'possede' => 'boolean',
        'date_achat' => 'date',
    ];

    // Relations
    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }
}
