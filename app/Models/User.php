<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * Represents a user in the EduWave platform, which can be a student, teacher, or admin.
 * This model handles user authentication, profile information, and relationships with other entities
 * such as courses, progress, groups, rewards, echo cards, and collaboration events.
 *
 *
 * @property int $id The unique identifier for the user.
 * @property string $name The full name of the user.
 * @property string $email The email address of the user, used for login and notifications.
 * @property string $password The hashed password for the user (hidden from serialization).
 * @property string $role The role of the user (e.g., student, teacher, admin).
 * @property Carbon $created_at Timestamp when the user was created.
 * @property Carbon $updated_at Timestamp when the user was last updated.
 * @property Collection|Course[] $courses Courses created by the user (for teachers).
 * @property Collection|UserProgress[] $user_progresses The user's progress on lessons.
 * @property Collection|Group[] $groups Groups created by the user.
 * @property Collection|Reward[] $rewards Rewards earned by the user (points and badges).
 * @property Collection|EchoCard[] $echo_cards Echo cards created by the user (from discussions).
 * @property Collection|GroupMember[] $group_members Groups the user is a member of.
 * @property Collection|GroupMessage[] $group_messages Messages sent by the user in groups.
 * @property Collection|CollaborationEvent[] $collaboration_events Collaboration events the user participates in.
 *
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRole($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static User|null find($id) Find a user by their ID.
 * @method static where(string $string, mixed $email)
 * @method static create(array $array)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays and JSON serialization.
     * This ensures sensitive data like passwords are not exposed in API responses.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that are mass assignable.
     * These fields can be filled using mass assignment (e.g., during user creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile',
        'provider'
    ];

    /**
     * Get the courses created by the user.
     *
     * This defines a one-to-many relationship between the User and Course models.
     * A user (typically a teacher) can create multiple courses.
     *
     * @return HasMany The relationship to the Course model.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    /**
     * Get the user's progress on lessons.
     *
     * This defines a one-to-many relationship between the User and UserProgress models.
     * Tracks the user's progress on various lessons (e.g., started, completed, failed).
     *
     * @return HasMany The relationship to the UserProgress model.
     */
    public function user_progresses(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * Get the groups created by the user.
     *
     * This defines a one-to-many relationship between the User and Group models.
     * A user can create multiple groups for collaboration.
     *
     * @return HasMany The relationship to the Group model.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    /**
     * Get the rewards earned by the user.
     *
     * This defines a one-to-many relationship between the User and Reward models.
     * Rewards include points and badges earned through activities like quizzes and collaboration.
     *
     * @return HasMany The relationship to the Reward model.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    /**
     * Get the echo cards created by the user.
     *
     * This defines a one-to-many relationship between the User and EchoCard models.
     * Echo cards are mini-lessons or flashcards generated from user discussions (e.g., chats, whiteboards).
     *
     * @return HasMany The relationship to the EchoCard model.
     */
    public function echo_cards(): HasMany
    {
        return $this->hasMany(EchoCard::class, 'created_by');
    }

    /**
     * Get the groups the user is a member of.
     *
     * This defines a one-to-many relationship between the User and GroupMember models.
     * Tracks the user's membership in various groups for collaboration.
     *
     * @return HasMany The relationship to the GroupMember model.
     */
    public function group_members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get the messages sent by the user in groups.
     *
     * This defines a one-to-many relationship between the User and GroupMessage models.
     * Tracks messages sent by the user in group chats.
     *
     * @return HasMany The relationship to the GroupMessage model.
     */
    public function group_messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class);
    }

    /**
     * Get the collaboration events the user participates in.
     *
     * This defines a one-to-many relationship between the User and CollaborationEvent models.
     * Tracks the user's participation in real-time collaboration sessions (e.g., chat, whiteboard, video).
     *
     * @return HasMany The relationship to the CollaborationEvent model.
     */
    public function collaboration_events(): HasMany
    {
        return $this->hasMany(CollaborationEvent::class);
    }

    /**
     * Get the echo sessions the user has created
     *
     * @return HasMany The relationship to the EchoSession Model
     */
    public function echo_session(): HasMany
    {
        return $this->hasMany(EchoSession::class);
    }

    /**
     * @return HasMany The relationship to the Recommendation Model.
     */
    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }
}
