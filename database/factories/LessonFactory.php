<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => $this->faker->sentence(4),
            'type' => $this->faker->randomElement(['video', 'quiz', 'game', 'html']),
            'html' => '', // you can later override this with a state()
            'content' => $this->faker->paragraphs(2, true),
            'order_no' => $this->faker->numberBetween(1, 10)
        ];
    }

    /**
     * Optionally include static HTML from a file for html-type lessons.
     */
    public function withHtmlContent(string $path = 'factory/index.html'): self
    {
        return $this->state(function () use ($path) {
            return [
                'type' => 'html',
                'html' => $path,
            ];
        });
    }
}
