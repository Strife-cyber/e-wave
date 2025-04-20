<?php

namespace App\Http\Controllers;

use App\Models\Collaboration;
use Illuminate\Http\Request;
use App\Http\Requests\CollaborationStoreRequest;
use App\Http\Requests\CollaborationUpdateRequest;

class CollaborationController extends Controller
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
    public function store(App\Http\Requests\CollaborationStoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Collaboration $collaboration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collaboration $collaboration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(App\Http\Requests\CollaborationUpdateRequest $request, Collaboration $collaboration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collaboration $collaboration)
    {
        //
    }
}
