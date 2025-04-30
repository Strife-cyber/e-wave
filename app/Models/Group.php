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
 * Class Group
 *
 * Represents a group in the EduWave platform, which facilitates collaboration among users.
 * Groups are associated with a courses and created by a user (typically a teacher or student).
 * They support real-time collaboration features like chat, whiteboards, and video calls through
 * related group messages and collaboration records.
 *
 *
 * @property int $id The unique identifier for the group.
 * @property string $name The name of the group.
 * @property int $course_id The ID of the courses this group is associated with.
 * @property int $created_by The ID of the user who created the group.
 * @property Carbon $created_at Timestamp when the group was created.
 * @property Carbon $updated_at Timestamp when the group was last updated.
 * @property bool $bright Indicates if the group is marked as "bright" (e.g., highlighted or active).
 * @property Course $courses The courses this group is associated with.
 * @property User $user The user who created this group.
 * @property Collection|GroupMember[] $group_members Members of this group.
 * @property Collection|GroupMessage[] $group_messages Messages sent within this group.
 * @property Collection|Collaboration[] $collaborations Collaboration events associated with this group.
 *
 * @method static Builder|Group newModelQuery()
 * @method static Builder|Group newQuery()
 * @method static Builder|Group query()
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group whereCourseId($value)
 * @method static Builder|Group whereCreatedBy($value)
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereUpdatedAt($value)
 * @method static Builder|Group whereBright($value)
 */
class Group extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures that specific columns are automatically cast to the correct PHP types when accessed.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'course_id' => 'int',
        'created_by' => 'int',
        'bright' => 'bool',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment (e.g., during group creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'course_id',
        'created_by',
        'bright',
    ];

    /**
     * Get the courses this group is associated with.
     *
     * This defines a many-to-one relationship between the Group and Course models.
     * Each group is associated with exactly one courses.
     *
     * @return BelongsTo The relationship to the Course model.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user who created this group.
     *
     * This defines a many-to-one relationship between the Group and User models.
     * Each group is created by exactly one user (typically a teacher or student).
     *
     * @return BelongsTo The relationship to the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the members of this group.
     *
     * This defines a one-to-many relationship between the Group and GroupMember models.
     * A group can have multiple members (users who have joined the group).
     *
     * @return HasMany The relationship to the GroupMember model.
     */
    public function group_members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get the messages sent within this group.
     *
     * This defines a one-to-many relationship between the Group and GroupMessage models.
     * A group can have multiple messages sent by its members.
     *
     * @return HasMany The relationship to the GroupMessage model.
     */
    public function group_messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class);
    }

    /**
     * Get the collaboration events associated with this group.
     *
     * This defines a one-to-many relationship between the Group and Collaboration models.
     * A group can have multiple collaboration events (e.g., chat, whiteboard, video sessions).
     *
     * @return HasMany The relationship to the Collaboration model.
     */
    public function collaborations(): HasMany
    {
        return $this->hasMany(Collaboration::class);
    }
}
