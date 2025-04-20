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
 * Class Lesson
 *
 * Represents a lesson in the EduWave platform, which belongs to a course and can be of various types
 * (e.g., video, quiz, game). Lessons are the core learning units that users interact with, and they
 * can have associated progress tracking and echo cards generated from discussions.
 *
 * @package App\Models
 *
 * @property int $id The unique identifier for the lesson.
 * @property int $course_id The ID of the course this lesson belongs to.
 * @property string $title The title of the lesson.
 * @property string $type The type of the lesson (e.g., 'video', 'quiz', 'game').
 * @property string $content The content of the lesson, typically stored as HTML or text.
 * @property int $order_no The order of the lesson within its course.
 * @property Carbon $created_at Timestamp when the lesson was created.
 * @property Carbon $updated_at Timestamp when the lesson was last updated.
 * @property bool $bright Indicates if the lesson is marked as "bright" (e.g., highlighted or active).
 *
 * @property Course $course The course this lesson belongs to.
 * @property Collection|UserProgress[] $user_progresses Progress records of users for this lesson.
 * @property Collection|EchoCard[] $echo_cards Echo cards generated from discussions related to this lesson.
 *
 * @method static Builder|Lesson newModelQuery()
 * @method static Builder|Lesson newQuery()
 * @method static Builder|Lesson query()
 * @method static Builder|Lesson whereId($value)
 * @method static Builder|Lesson whereCourseId($value)
 * @method static Builder|Lesson whereTitle($value)
 * @method static Builder|Lesson whereType($value)
 * @method static Builder|Lesson whereContent($value)
 * @method static Builder|Lesson whereOrderNo($value)
 * @method static Builder|Lesson whereCreatedAt($value)
 * @method static Builder|Lesson whereUpdatedAt($value)
 * @method static Builder|Lesson whereBright($value)
 */
class Lesson extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lessons';

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures that specific columns are automatically cast to the correct PHP types when accessed.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'course_id' => 'int',
        'order_no' => 'int',
        'bright' => 'bool'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment (e.g., during lesson creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'type',
        'content',
        'order_no',
        'bright'
    ];

    /**
     * Get the course that this lesson belongs to.
     *
     * This defines a many-to-one relationship between the Lesson and Course models.
     * Each lesson is associated with exactly one course.
     *
     * @return BelongsTo The relationship to the Course model.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user progress records associated with this lesson.
     *
     * This defines a one-to-many relationship between the Lesson and UserProgress models.
     * Tracks the progress of multiple users on this lesson (e.g., started, completed, failed).
     *
     * @return HasMany The relationship to the UserProgress model.
     */
    public function user_progresses(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * Get the echo cards associated with this lesson.
     *
     * This defines a one-to-many relationship between the Lesson and EchoCard models.
     * Echo cards are mini-lessons or flashcards generated from discussions related to this lesson.
     *
     * @return HasMany The relationship to the EchoCard model.
     */
    public function echo_cards(): HasMany
    {
        return $this->hasMany(EchoCard::class);
    }
}
