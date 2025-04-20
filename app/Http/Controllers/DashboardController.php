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
    public function index(): Response
    {
        $auth_user = auth()->user();
        $user = User::find($auth_user['id']);
        $progress = $user->user_progresses()->with('lesson.course')->get();
        $rewards = $user->rewards()->sum('points');
        $badges = Badge::whereIn('id', $user->rewards()->pluck('badge_id'))->get();
        $groups = $user->group_members()->with('latest_message')->get();
        $echoCards = EchoCard::latest()->take(3)->get();
        $courses = Course::whereIn('id', $progress->pluck('lesson.course_id'))->get();

        return Inertia::render('Dashboard', [
            'user' => $user,
            'progress' => $progress,
            'rewards' => $rewards,
            'badges' => $badges,
            'groups' => $groups,
            'echoCards' => $echoCards,
            'courses' => $courses
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
