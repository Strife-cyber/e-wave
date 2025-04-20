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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class GroupMember
 *
 * Represents a membership record in the EduWave platform, linking a user to a group.
 * This model tracks when a user joins a group, enabling collaboration features like group chats,
 * whiteboards, and video calls within the group.
 *
 * @package App\Models
 *
 * @property int $id The unique identifier for the group member record.
 * @property int $group_id The ID of the group the user is a member of.
 * @property int $user_id The ID of the user who is a member of the group.
 * @property Carbon $joined_at Timestamp when the user joined the group.
 * @property Carbon $created_at Timestamp when the group member record was created.
 * @property Carbon $updated_at Timestamp when the group member record was last updated.
 *
 * @property Group $group The group associated with this membership.
 * @property User $user The user associated with this membership.
 *
 * @method static Builder|GroupMember newModelQuery()
 * @method static Builder|GroupMember newQuery()
 * @method static Builder|GroupMember query()
 * @method static Builder|GroupMember whereId($value)
 * @method static Builder|GroupMember whereGroupId($value)
 * @method static Builder|GroupMember whereUserId($value)
 * @method static Builder|GroupMember whereJoinedAt($value)
 * @method static Builder|GroupMember whereCreatedAt($value)
 * @method static Builder|GroupMember whereUpdatedAt($value)
 */
class GroupMember extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'group_members';

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures that specific columns are automatically cast to the correct PHP types when accessed.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'group_id' => 'int',
        'user_id' => 'int',
        'joined_at' => 'datetime'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment (e.g., during group member creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_id',
        'user_id',
        'joined_at'
    ];

    /**
     * Get the group associated with this membership.
     *
     * This defines a many-to-one relationship between the GroupMember and Group models.
     * Each group member record is associated with exactly one group.
     *
     * @return BelongsTo The relationship to the Group model.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user associated with this membership.
     *
     * This defines a many-to-one relationship between the GroupMember and User models.
     * Each group member record is associated with exactly one user.
     *
     * @return BelongsTo The relationship to the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
    * Get the messages associated with this membership.
    *
    * This defines a one-to-many relationship between the GroupMember and GroupMessage models.
    * Each group member record is associated with many group messages.
    *
    * @return HasMany The relationship to the Group Message model.
    */
    public function group_messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class);
    }

    /**
     * Get the latest message associated with this membership.
     *
     * This defines a one-to-one relationship between the GroupMember and latest GroupMessage model.
     * Each group member record is associated with the last group message.
     *
     * @return HasOne The relationship to the Group Message model.
     */
    public function latest_message(): HasOne
    {
        return $this->hasOne(GroupMessage::class, 'group_id', 'group_id')->latestOfMany();
    }
}
