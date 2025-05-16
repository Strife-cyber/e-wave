<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecommendationRequest;
use App\Http\Requests\UpdateRecommendationRequest;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RecommendationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = User::find(auth()->user()->getAuthIdentifier());
        $recommendations = $user->recommendations()->with('content')->orderBy('priority')->get();

        return response()->json($recommendations);
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
    public function store(StoreRecommendationRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->getAuthIdentifier();

        Recommendation::create($validated);

        return response()->json(['message' => 'Recommendations generated']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Recommendation $recommendation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recommendation $recommendation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecommendationRequest $request, Recommendation $recommendation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recommendation $recommendation)
    {
        //
    }
}
