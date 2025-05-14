<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(mixed $validated)
 */
class EchoSession extends Model
{
    /** @use HasFactory<\Database\Factories\EchoSessionFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'content_id', 'score', 'reflection'];

    // Cast properties as needed
    protected $casts = [
        'score' => 'integer',
        'reflection' => 'string',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
