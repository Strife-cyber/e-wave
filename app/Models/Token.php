<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static where(string $string, string $string1)
 */
class Token extends Model
{
    /** @use HasFactory<\Database\Factories\TokenFactory> */
    use HasFactory;

    protected $fillable = [
        'token',
        'type',
        'expires_at',
        'user_id',
    ];
}
