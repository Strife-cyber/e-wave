<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Course;
use App\Models\EchoCard;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display the dashboard.
     */
    public function index(): Response
    {
        // Get the authenticated user, or null if not authenticated
        $auth_user = auth()->user();

        // If no user is authenticated, return default/empty data
        if (!$auth_user) {
            return Inertia::render('Dashboard', [
                'user' => null,
                'progress' => [],
                'rewards' => 0,
                'badges' => [],
                'groups' => [],
                'echoCards' => [],
                'courses' => [],
            ]);
        }

        // Since $auth_user is already a User instance, no need to fetch again
        $user = $auth_user;

        // Fetch user progress with related lesson and course data
        $progress = $user->user_progresses()
            ->with(['lesson' => function ($query) {
                $query->with('course'); // Ensure the course relationship is loaded
            }])
            ->get();

        // Fetch rewards and badges in a single query
        $rewardsData = $user->rewards()->selectRaw('SUM(points) as total_points, badge_id')
            ->groupBy('badge_id')
            ->get();
        $rewards = $rewardsData->sum('total_points') ?? 0; // Total points, default to 0
        $badgeIds = $rewardsData->pluck('badge_id')->filter(); // Filter out null badge_ids
        $badges = $badgeIds->isNotEmpty() ? Badge::whereIn('id', $badgeIds)->get() : collect([]);

        // Fetch groups with the latest message
        $groups = $user->group_members()
            ->with('group')->with('latest_message')
            ->get();

        // Fetch the latest 3 echo cards
        $echoCards = EchoCard::latest()->take(3)->get();

        // Fetch courses based on progress
        $courseIds = $progress->pluck('lesson.course.id')->flatten()->filter()->unique();
        $courses = $courseIds->isNotEmpty() ? Course::whereIn('id', $courseIds)->get() : collect([]);

        return Inertia::render('Dashboard', [
            'user' => $user,
            'progress' => $progress->isNotEmpty() ? $progress : collect([]),
            'rewards' => $rewards,
            'badges' => $badges->isNotEmpty() ? $badges : collect([]),
            'groups' => $groups->isNotEmpty() ? $groups : collect([]),
            'echoCards' => $echoCards->isNotEmpty() ? $echoCards : collect([]),
            'courses' => $courses->isNotEmpty() ? $courses : collect([]),
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
    public function store()
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
    public function update()
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
