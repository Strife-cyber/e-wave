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
 * Class EchoCard
 *
 * Represents an echo card in the EduWave platform, a unique feature that captures and transforms
 * student discussions, questions, or ideas from chats or whiteboards into mini-lessons or flashcards.
 * Echo cards are associated with a lesson and created by a user, enabling community-driven learning content.
 *
 *
 * @property int $id The unique identifier for the echo card.
 * @property string $content The content of the echo card (e.g., a quiz question like "What pulls objects down? A) Gravity B) Magic").
 * @property string $source_type The type of source the echo card was generated from (e.g., 'chat', 'whiteboard', 'question').
 * @property string $source_content The original content from the source (e.g., a chat message or whiteboard note).
 * @property int $lesson_id The ID of the lesson this echo card is associated with.
 * @property int $created_by The ID of the user who created the echo card.
 * @property Carbon $created_at Timestamp when the echo card was created.
 * @property Carbon $updated_at Timestamp when the echo card was last updated.
 * @property Lesson $lesson The lesson this echo card is associated with.
 * @property User $user The user who created this echo card.
 *
 * @method static Builder|EchoCard newModelQuery()
 * @method static Builder|EchoCard newQuery()
 * @method static Builder|EchoCard query()
 * @method static Builder|EchoCard whereId($value)
 * @method static Builder|EchoCard whereContent($value)
 * @method static Builder|EchoCard whereSourceType($value)
 * @method static Builder|EchoCard whereSourceContent($value)
 * @method static Builder|EchoCard whereLessonId($value)
 * @method static Builder|EchoCard whereCreatedBy($value)
 * @method static Builder|EchoCard whereCreatedAt($value)
 * @method static Builder|EchoCard whereUpdatedAt($value)
 * @method static Builder|EchoCard latest()
 */
class EchoCard extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'echo_cards';

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures that specific columns are automatically cast to the correct PHP types when accessed.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lesson_id' => 'int',
        'created_by' => 'int',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment (e.g., during echo card creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'source_type',
        'source_content',
        'lesson_id',
        'created_by',
    ];

    /**
     * Get the lesson this echo card is associated with.
     *
     * This defines a many-to-one relationship between the EchoCard and Lesson models.
     * Each echo card is associated with exactly one lesson.
     *
     * @return BelongsTo The relationship to the Lesson model.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the user who created this echo card.
     *
     * This defines a many-to-one relationship between the EchoCard and User models.
     * Each echo card is created by exactly one user.
     *
     * @return BelongsTo The relationship to the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
