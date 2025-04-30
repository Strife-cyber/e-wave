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
 * Class UserProgress
 *
 * Represents a user's progress on a specific lesson in the EduWave platform. This model tracks
 * the status, score, and completion details of a user's interaction with a lesson, enabling features
 * like adaptive learning paths and progress tracking.
 *
 *
 * @property int $id The unique identifier for the user progress record.
 * @property int $user_id The ID of the user associated with this progress record.
 * @property int $lesson_id The ID of the lesson associated with this progress record.
 * @property string $status The status of the user's progress (e.g., 'started', 'completed', 'failed').
 * @property int $score The score achieved by the user on this lesson (0-100).
 * @property Carbon|null $completed_at The timestamp when the lesson was completed, if applicable.
 * @property Carbon $created_at Timestamp when the progress record was created.
 * @property Carbon $updated_at Timestamp when the progress record was last updated.
 * @property User $user The user associated with this progress record.
 * @property Lesson $lesson The lesson associated with this progress record.
 *
 * @method static Builder|UserProgress newModelQuery()
 * @method static Builder|UserProgress newQuery()
 * @method static Builder|UserProgress query()
 * @method static Builder|UserProgress whereId($value)
 * @method static Builder|UserProgress whereUserId($value)
 * @method static Builder|UserProgress whereLessonId($value)
 * @method static Builder|UserProgress whereStatus($value)
 * @method static Builder|UserProgress whereScore($value)
 * @method static Builder|UserProgress whereCompletedAt($value)
 * @method static Builder|UserProgress whereCreatedAt($value)
 * @method static Builder|UserProgress whereUpdatedAt($value)
 */
class UserProgress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_progress';

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures that specific columns are automatically cast to the correct PHP types when accessed.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'int',
        'lesson_id' => 'int',
        'score' => 'int',
        'completed_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment (e.g., during progress record creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'lesson_id',
        'status',
        'score',
        'completed_at',
    ];

    /**
     * Get the user associated with this progress record.
     *
     * This defines a many-to-one relationship between the UserProgress and User models.
     * Each progress record is associated with exactly one user.
     *
     * @return BelongsTo The relationship to the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson associated with this progress record.
     *
     * This defines a many-to-one relationship between the UserProgress and Lesson models.
     * Each progress record is associated with exactly one lesson.
     *
     * @return BelongsTo The relationship to the Lesson model.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
