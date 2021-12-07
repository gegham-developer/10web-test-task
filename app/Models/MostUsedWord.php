<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MostUsedWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'word',
        'date',
        'count'
    ];

    public $timestamps = false;

    public function mostUsedWord($date) {
        return self::whereDate($date)->first();
    }
}
