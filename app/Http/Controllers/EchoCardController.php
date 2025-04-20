<?php

namespace App\Http\Controllers;

use App\Models\EchoCard;
use Illuminate\Http\Request;
use App\Http\Requests\EchoCardStoreRequest;
use App\Http\Requests\EchoCardUpdateRequest;

class EchoCardController extends Controller
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
    public function store(App\Http\Requests\EchoCardStoreRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EchoCard $echoCard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EchoCard $echoCard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(App\Http\Requests\EchoCardUpdateRequest $request, EchoCard $echoCard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EchoCard $echoCard)
    {
        //
    }
}
