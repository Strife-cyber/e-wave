<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonStoreRequest;
use App\Http\Requests\LessonUpdateRequest;
use App\Models\Course;
use App\Models\Lesson;
use Inertia\Inertia;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(LessonStoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        return Inertia::render('lessons/Show', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LessonUpdateRequest $request, Lesson $lesson)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson)
    {
        //
    }

    public function lessons(Course $course)
    {
        $lessons = $course->lessons;

        return Inertia::render('lessons/Lessons', [
            'lessons' => $lessons,
        ]);
    }
}
