<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Badge
 *
 * Represents a badge in the EduWave platform, which is part of the gamification system.
 * Badges are awarded to users when they reach specific point milestones, encouraging engagement
 * and motivation through rewards.
 *
 * @package App\Models
 *
 * @property int $id The unique identifier for the badge.
 * @property string $name The name of the badge (e.g., "Explorer Badge").
 * @property string $image_url The URL to the badge's image for display purposes.
 * @property int $points_required The number of points a user must earn to receive this badge.
 * @property Carbon $created_at Timestamp when the badge was created.
 * @property Carbon $updated_at Timestamp when the badge was last updated.
 *
 * @property Collection|Reward[] $rewards The rewards associated with this badge, linking it to users.
 *
 * @method static Builder|Badge newModelQuery()
 * @method static Builder|Badge newQuery()
 * @method static Builder|Badge query()
 * @method static Builder|Badge whereId($value)
 * @method static Builder|Badge whereName($value)
 * @method static Builder|Badge whereImageUrl($value)
 * @method static Builder|Badge wherePointsRequired($value)
 * @method static Builder|Badge whereCreatedAt($value)
 * @method static Builder|Badge whereUpdatedAt($value)
 * @method static Builder|Badge whereIn(string $column, \Illuminate\Support\Collection|array $values)
 */
class Badge extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'badges';

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures that specific columns are automatically cast to the correct PHP types when accessed.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'points_required' => 'int'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment (e.g., during badge creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image_url',
        'points_required'
    ];

    /**
     * Get the rewards associated with this badge.
     *
     * This defines a one-to-many relationship between the Badge and Reward models.
     * A badge can be awarded to multiple users through the rewards system.
     *
     * @return HasMany The relationship to the Reward model.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }
}
