<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(mixed $validated)
 */
class Recommendation extends Model
{
    /** @use HasFactory<\Database\Factories\RecommendationFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'content_id', 'priority'];

    // Cast properties as needed
    protected $casts = [
        'priority' => 'integer',
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
