<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Reward
 *
 * Represents a reward in the EduWave platform, which is part of the gamification system.
 * Rewards track points earned by users and optionally link to a badge when a milestone is reached.
 * This model helps motivate users by rewarding their engagement and progress.
 *
 *
 * @property int $id The unique identifier for the reward record.
 * @property int $user_id The ID of the user who earned this reward.
 * @property int $points The number of points earned in this reward instance.
 * @property int|null $badge_id The ID of the badge associated with this reward, if any.
 * @property Carbon $created_at Timestamp when the reward was created.
 * @property Carbon $updated_at Timestamp when the reward was last updated.
 * @property User $user The user who earned this reward.
 * @property Badge|null $badge The badge associated with this reward, if applicable.
 *
 * @method static Builder|Reward newModelQuery()
 * @method static Builder|Reward newQuery()
 * @method static Builder|Reward query()
 * @method static Builder|Reward whereId($value)
 * @method static Builder|Reward whereUserId($value)
 * @method static Builder|Reward wherePoints($value)
 * @method static Builder|Reward whereBadgeId($value)
 * @method static Builder|Reward whereCreatedAt($value)
 * @method static Builder|Reward whereUpdatedAt($value)
 */
class Reward extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rewards';

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures that specific columns are automatically cast to the correct PHP types when accessed.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'int',
        'points' => 'int',
        'badge_id' => 'int',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment (e.g., during reward creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'points',
        'badge_id',
    ];

    /**
     * Get the user who earned this reward.
     *
     * This defines a many-to-one relationship between the Reward and User models.
     * Each reward is associated with exactly one user.
     *
     * @return BelongsTo The relationship to the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the badge associated with this reward, if any.
     *
     * This defines a many-to-one relationship between the Reward and Badge models.
     * A reward may optionally be linked to a badge when a user reaches a point milestone.
     *
     * @return BelongsTo The relationship to the Badge model.
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }
}
