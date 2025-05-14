<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    /** @use HasFactory<\Database\Factories\ContentFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'body', 'type', 'level', 'persona_id'
    ];

    protected $casts = [
        'type' => 'string',
        'level' => 'string',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }
}
