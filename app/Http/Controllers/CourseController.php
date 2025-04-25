<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\CourseStoreRequest;
use App\Http\Requests\CourseUpdateRequest;
use Inertia\Inertia;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $courses = Course::with('lessons');

        $search = $request->input('search');

        if ($search) {
            $courses->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        }

        $courses = $courses->paginate(12)->withQueryString();

        return Inertia::render('courses/Index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(App\Http\Requests\CourseStoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        // Load the relationships on the already-resolved $course model
        $course->load('lessons', 'groups');

        return Inertia::render('courses/Show', [
            'course' => $course,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(App\Http\Requests\CourseUpdateRequest $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }

    /**
     * Get the courses specific to the user
     */
    public function courses()
    {
        $auth = auth()->user();
        $user = User::find($auth->id);

        $progress = $user->user_progresses()
            ->with(['lesson' => function ($query) {
                $query->with('course'); // Ensure the course relationship is loaded
            }])
            ->get();

        $courseIds = $progress->pluck('lesson.course.id')->flatten()->filter()->unique();
        $courses = $courseIds->isNotEmpty() ? Course::whereIn('id', $courseIds)->get() : collect([]);

        return Inertia::render('courses/Courses', [
            'courses' => $courses,
            'progress' => $progress,
        ]);
    }
}
