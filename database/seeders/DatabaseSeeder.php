<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\CollaborationEvent;
use App\Models\Course;
use App\Models\EchoCard;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Lesson;
use App\Models\Reward;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserProgress;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $teachers = User::factory()->count(3)->state(['role' => 'teacher'])->create();
        $students = User::factory()->count(7)->state(['role' => 'student'])->create();
        $users = $teachers->merge($students);

        $badges = Badge::factory()->count(10)->create();

        $users->each(function ($user) use ($badges) {
            if ($user->role === 'teacher') {
                $courses = Course::factory()->count(2)->create([
                    'created_by' => $user->id,
                ]);
                $courses->each(function ($course) {
                    Lesson::factory()->count(5)->create([
                        'course_id' => $course->id,
                    ]);
                });
            }

            $lessons = Lesson::all()->random(3);
            if ($lessons->isNotEmpty()) {
                $lessons->each(function ($lesson) use ($user) {
                    UserProgress::factory()->create([
                        'user_id' => $user->id,
                        'lesson_id' => $lesson->id,
                    ]);
                });
            }

            Reward::factory()->count(3)->create([
                'user_id' => $user->id,
                'badge_id' => $badges->random()->id,
            ]);

            $group = Group::factory()->create(['created_by' => $user->id]);
            GroupMember::factory()->create([
                'group_id' => $group->id,
                'user_id' => $user->id,
            ]);

            GroupMessage::factory()->count(5)->create([
                'group_id' => $group->id,
                'user_id' => $user->id,
            ]);

            EchoCard::factory()->count(2)->create([
                'created_by' => $user->id,
                'lesson_id' => $lessons->random()->id,
            ]);

            CollaborationEvent::factory()->count(2)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
