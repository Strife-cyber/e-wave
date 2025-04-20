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
 * Class CollaborationEvent
 *
 * Represents a collaboration event in the EduWave platform, tracking a user's participation
 * in a collaboration session (e.g., chat, whiteboard, or video call). This model records when
 * a user joins and leaves a session, enabling detailed tracking of engagement within groups.
 *
 * @package App\Models
 *
 * @property int $id The unique identifier for the collaboration event.
 * @property int $collaboration_id The ID of the collaboration session this event is associated with.
 * @property int $user_id The ID of the user participating in the collaboration session.
 * @property Carbon $joined_at Timestamp when the user joined the collaboration session.
 * @property Carbon|null $left_at Timestamp when the user left the collaboration session, if applicable.
 * @property Carbon $created_at Timestamp when the collaboration event was created.
 * @property Carbon $updated_at Timestamp when the collaboration event was last updated.
 *
 * @property Collaboration $collaboration The collaboration session associated with this event.
 * @property User $user The user who participated in this collaboration event.
 *
 * @method static Builder|CollaborationEvent newModelQuery()
 * @method static Builder|CollaborationEvent newQuery()
 * @method static Builder|CollaborationEvent query()
 * @method static Builder|CollaborationEvent whereId($value)
 * @method static Builder|CollaborationEvent whereCollaborationId($value)
 * @method static Builder|CollaborationEvent whereUserId($value)
 * @method static Builder|CollaborationEvent whereJoinedAt($value)
 * @method static Builder|CollaborationEvent whereLeftAt($value)
 * @method static Builder|CollaborationEvent whereCreatedAt($value)
 * @method static Builder|CollaborationEvent whereUpdatedAt($value)
 */
class CollaborationEvent extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'collaboration_events';

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures that specific columns are automatically cast to the correct PHP types when accessed.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'collaboration_id' => 'int',
        'user_id' => 'int',
        'joined_at' => 'datetime',
        'left_at' => 'datetime'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment (e.g., during collaboration event creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'collaboration_id',
        'user_id',
        'joined_at',
        'left_at'
    ];

    /**
     * Get the collaboration session associated with this event.
     *
     * This defines a many-to-one relationship between the CollaborationEvent and Collaboration models.
     * Each collaboration event is associated with exactly one collaboration session.
     *
     * @return BelongsTo The relationship to the Collaboration model.
     */
    public function collaboration(): BelongsTo
    {
        return $this->belongsTo(Collaboration::class);
    }

    /**
     * Get the user who participated in this collaboration event.
     *
     * This defines a many-to-one relationship between the CollaborationEvent and User models.
     * Each collaboration event is associated with exactly one user.
     *
     * @return BelongsTo The relationship to the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
