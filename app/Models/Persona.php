<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    /** @use HasFactory<\Database\Factories\PersonaFactory> */
    use HasFactory;

    protected $fillable = ['name', 'role', 'personality'];

    // Cast 'personality' as JSON
    protected $casts = [
        'personality' => 'array',
    ];

    // Relationships
    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
