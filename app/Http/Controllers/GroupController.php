<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Inertia\Inertia;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auth = auth()->user();
        $user = User::find($auth->id);
        $groups = $user->groups;
        $members = $user->group_members;
        foreach ($members as $member) {
            $groups->add($member->group);
        }

        return Inertia::render('groups/Index', [
            'groups' => $groups,
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
    public function store(App\Http\Requests\GroupStoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $auth = auth()->user();
        $user = User::find($auth->id);
        $members = $group->group_members;
        $members->load(['user', 'latest_message']);

        return Inertia::render('groups/Show', [
            'groupMembers' => $members,
            'group' => $group,
            'currentUser' => $user,
            'success' => '',
            'attachment' => [],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(App\Http\Requests\GroupUpdateRequest $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        //
    }
}
