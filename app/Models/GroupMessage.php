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
 * Class GroupMessage
 *
 * Represents a message sent within a group in the EduWave platform. This model tracks messages
 * exchanged during group collaboration, including the content, sender, and timestamp. It is part
 * of the real-time collaboration suite, enabling communication among group members.
 *
 *
 * @property int $id The unique identifier for the group message.
 * @property int $group_id The ID of the group this message belongs to.
 * @property int $user_id The ID of the user who sent this message.
 * @property string $content The content of the message.
 * @property Carbon $sent_at Timestamp when the message was sent.
 * @property Carbon $created_at Timestamp when the message record was created.
 * @property Carbon $updated_at Timestamp when the message record was last updated.
 * @property Group $group The group this message belongs to.
 * @property User $user The user who sent this message.
 *
 * @method static Builder|GroupMessage newModelQuery()
 * @method static Builder|GroupMessage newQuery()
 * @method static Builder|GroupMessage query()
 * @method static Builder|GroupMessage whereId($value)
 * @method static Builder|GroupMessage whereGroupId($value)
 * @method static Builder|GroupMessage whereUserId($value)
 * @method static Builder|GroupMessage whereContent($value)
 * @method static Builder|GroupMessage whereSentAt($value)
 * @method static Builder|GroupMessage whereCreatedAt($value)
 * @method static Builder|GroupMessage whereUpdatedAt($value)
 */
class GroupMessage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'group_messages';

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
        'sent_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment (e.g., during group message creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_id',
        'user_id',
        'content',
        'sent_at',
    ];

    /**
     * Get the group this message belongs to.
     *
     * This defines a many-to-one relationship between the GroupMessage and Group models.
     * Each message is associated with exactly one group.
     *
     * @return BelongsTo The relationship to the Group model.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user who sent this message.
     *
     * This defines a many-to-one relationship between the GroupMessage and User models.
     * Each message is sent by exactly one user.
     *
     * @return BelongsTo The relationship to the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
