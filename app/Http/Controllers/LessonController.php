<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonStoreRequest;
use App\Http\Requests\LessonUpdateRequest;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

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
    public function create(): Response
    {
        return Inertia::render('contributions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LessonStoreRequest $request): JsonResponse
    {
        // Validate that only one file is uploaded
        $files = $request->file('files');
        if (count($files) !== 1) {
            return response()->json(['error' => 'Exactly one file (video or HTML) is required.'], 422);
        }

        $file = $files[0];
        $extension = strtolower($file->getClientOriginalExtension());

        // Determine file type based on extension
        $videoExtensions = ['mp4', 'mov', 'avi'];
        $htmlExtensions = ['html'];
        if (in_array($extension, $videoExtensions)) {
            $fileType = 'video';
        } elseif (in_array($extension, $htmlExtensions)) {
            $fileType = 'html';
        } else {
            return response()->json(['error' => 'Invalid file extension. Allowed: mp4, mov, avi, html.'], 422);
        }

        // Generate unique identifier for the lesson
        $uniqueId = Str::uuid()->toString();

        // Store the file privately
        $filePath = $file->storeAs(
            "lessons/$uniqueId",
            "$uniqueId.$extension",
            'private' // Custom disk configured in config/filesystems.php
        );

        // Determine the next order_no for the course
        $courseId = $request->input('courseId');
        $maxOrderNo = Lesson::where('course_id', $courseId)->max('order_no') ?? 0;
        $nextOrderNo = $maxOrderNo + 1;

        // Create the lesson record
        $lesson = Lesson::create([
            'course_id' => $courseId,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'type' => $fileType,
            'content' => $filePath,
            'order_no' => $nextOrderNo,
            'bright' => true, // Default value as per your code
            'unique_id' => $uniqueId,
        ]);

        return response()->json();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $unique_id): Response
    {
        $lesson = Lesson::where('unique_id', $unique_id)->firstOrFail();

        // Authorization check (example: ensure user is enrolled)
        if (! auth()->user() /* || !auth()->user()->isEnrolledIn($lesson->course_id) */) {
            abort(403, 'Unauthorized access.');
        }

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

    public function lessons(Course $course): Response
    {
        $lessons = $course->lessons()->orderBy('order_no')->get();

        return Inertia::render('lessons/Lessons', [
            'lessons' => $lessons,
        ]);
    }
}
