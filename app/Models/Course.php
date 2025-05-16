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
 * Class Course
 *
 * Represents a courses in the EduWave platform, which is a collection of lessons created by a user
 * (typically a teacher). Courses can have associated groups for collaboration and are a core part of
 * the learning infrastructure.
 *
 *
 * @property int $id The unique identifier for the courses.
 * @property string $title The title of the courses.
 * @property string|null $description A brief description of the courses (optional).
 * @property string $text Additional text content related to the courses.
 * @property bool $bright Indicates if the courses is marked as "bright" (e.g., highlighted or active).
 * @property int $created_by The ID of the user who created the courses.
 * @property Carbon $created_at Timestamp when the courses was created.
 * @property Carbon $updated_at Timestamp when the courses was last updated.
 * @property User $user The user who created this courses.
 * @property Collection|Lesson[] $lessons Lessons that belong to this courses.
 * @property Collection|Group[] $groups Groups associated with this courses for collaboration.
 *
 * @method static Builder|Course newModelQuery()
 * @method static Builder|Course newQuery()
 * @method static Builder|Course query()
 * @method static Builder|Course whereId($value)
 * @method static Builder|Course whereTitle($value)
 * @method static Builder|Course whereDescription($value)
 * @method static Builder|Course whereText($value)
 * @method static Builder|Course whereBright($value)
 * @method static Builder|Course whereCreatedBy($value)
 * @method static Builder|Course whereCreatedAt($value)
 * @method static Builder|Course whereUpdatedAt($value)
 * @method static Builder|Course whereIn(string $column, \Illuminate\Support\Collection|array $values)
 * @method static create(mixed $validated)
 */
class Course extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'courses';

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures that specific columns are automatically cast to the correct PHP types when accessed.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bright' => 'bool',
        'created_by' => 'int',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using mass assignment (e.g., during courses creation or update).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'text',
        'bright',
        'created_by',
    ];

    /**
     * Get the user who created this courses.
     *
     * This defines a many-to-one relationship between the Course and User models.
     * Each courses is created by exactly one user (typically a teacher).
     *
     * @return BelongsTo The relationship to the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the lessons that belong to this courses.
     *
     * This defines a one-to-many relationship between the Course and Lesson models.
     * A courses can have multiple lessons associated with it.
     *
     * @return HasMany The relationship to the Lesson model.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * Get the groups associated with this courses.
     *
     * This defines a one-to-many relationship between the Course and Group models.
     * A courses can have multiple groups for collaboration among students.
     *
     * @return HasMany The relationship to the Group model.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
