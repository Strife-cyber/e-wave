<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auth = auth()->user();
        $user = User::find($auth->id);

        // Get groups (no pagination)
        $groups = $user->groups();

        // Get courses with filters and pagination
        $coursesQuery = $user->courses();

        // Apply course filters
        if (request()->has('course_search')) {
            $coursesQuery->where('title', 'like', '%' . request('course_search') . '%');
        }

        if (request()->has('course_bright')) {
            $bright = request('course_bright') === 'true';
            $coursesQuery->where('bright', $bright);
        }

        // Apply course sorting
        switch (request('course_sort', 'newest')) {
            case 'oldest':
                $coursesQuery->orderBy('created_at');
                break;
            case 'title_asc':
                $coursesQuery->orderBy('title');
                break;
            case 'title_desc':
                $coursesQuery->orderBy('title', 'desc');
                break;
            case 'newest':
            default:
                $coursesQuery->orderBy('created_at', 'desc');
                break;
        }

        // Get courses with lessons
        $courses = $coursesQuery->with('lessons')->paginate(4, ['*'], 'courses_page');

        // Get events with filters and pagination
        $eventsQuery = $user->collaboration_events();

        // Apply event filters
        if (request()->has('event_status')) {
            if (request('event_status') === 'active') {
                $eventsQuery->whereNull('left_at');
            } elseif (request('event_status') === 'inactive') {
                $eventsQuery->whereNotNull('left_at');
            }
        }

        if (request()->has('event_collaboration_id')) {
            $eventsQuery->where('collaboration_id', request('event_collaboration_id'));
        }

        if (request()->has('event_date_from')) {
            $eventsQuery->where('joined_at', '>=', request('event_date_from'));
        }

        if (request()->has('event_date_to')) {
            $eventsQuery->where('joined_at', '<=', request('event_date_to') . ' 23:59:59');
        }

        $events = $eventsQuery->paginate(5, ['*'], 'events_page');

        return Inertia::render('contributions/Index', [
            'groups' => $groups,
            'courses' => $courses,
            'events' => $events,
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
