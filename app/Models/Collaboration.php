<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Collaboration
 *
 * Represents a collaboration session in the EduWave platform, associated with a group.
 * Collaboration sessions enable real-time interactions such as chat, whiteboards, or video calls,
 * and track the start and end times of these sessions. Collaboration events link users to these sessions.
 *
 *
 * @property int $id The unique identifier for the collaboration session.
 * @property string $type The type of collaboration (e.g., 'chat', 'whiteboard', 'video').
 * @property int $group_id The ID of the group this collaboration session is associated with.
 * @property Carbon $started_at Timestamp when the collaboration session started.
 * @property Carbon|null $ended_at Timestamp when the collaboration session ended, if applicable.
 * @property Carbon $created_at Timestamp when the collaboration record was created.
 * @property Carbon $updated_at Timestamp when the collaboration record was last updated.
 * @property Group $group The group associated with this collaboration session.
 * @property Collection|CollaborationEvent[] $collaboration_events Events linking users to this collaboration session.
 *
 * @method static Builder|Collaboration newModelQuery()
 * @method static Builder|Collaboration newQuery()
 * @method static Builder|Collaboration query()
 * @method static Builder|Collaboration whereId($value)
 * @method static Builder|Collaboration whereType($value)
 * @method static Builder|Collaboration whereGroupId($value)
 * @method static Builder|Collaboration whereStartedAt($value)
 * @method static Builder|Collaboration whereEndedAt($value)
 * @method static Builder|Collaboration whereCreatedAt($value)
 * @method static Builder|Collaboration whereUpdatedAt($value)
 */
class Collaboration extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'collaborations';

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures that specific columns are automatically cast to the correct PHP types when accessed.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'group_id' => 'int',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment (e.g., during collaboration session creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'group_id',
        'started_at',
        'ended_at',
    ];

    /**
     * Get the group associated with this collaboration session.
     *
     * This defines a many-to-one relationship between the Collaboration and Group models.
     * Each collaboration session is associated with exactly one group.
     *
     * @return BelongsTo The relationship to the Group model.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the collaboration events associated with this session.
     *
     * This defines a one-to-many relationship between the Collaboration and CollaborationEvent models.
     * A collaboration session can have multiple events tracking user participation (e.g., joining or leaving).
     *
     * @return HasMany The relationship to the CollaborationEvent model.
     */
    public function collaboration_events(): HasMany
    {
        return $this->hasMany(CollaborationEvent::class);
    }
}
