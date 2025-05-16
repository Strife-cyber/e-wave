<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEchoSessionRequest;
use App\Http\Requests\UpdateEchoSessionRequest;
use App\Models\EchoSession;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class EchoSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $auth = auth()->user();
        $user = User::find($auth->getAuthIdentifier());
        $sessions = $user->echo_session();

        return response()->json($sessions);
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
    public function store(StoreEchoSessionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->getAuthIdentifier();

        $session = EchoSession::create($validated);

        return response()->json($session, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(EchoSession $echoSession)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EchoSession $echoSession)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEchoSessionRequest $request, EchoSession $echoSession)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EchoSession $echoSession)
    {
        //
    }
}
