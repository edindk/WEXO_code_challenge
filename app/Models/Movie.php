<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'movieTitle',
        'description',
        'releaseYear',
        'bgCover',
        'genre',
        'credits',
        'img',
        'id'
    ];
}
