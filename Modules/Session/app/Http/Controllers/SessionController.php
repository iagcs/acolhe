<?php

namespace Modules\Session\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Session\Actions\StoreSessionAction;
use Modules\Session\Http\Requests\StoreSessionRequest;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('session::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('session::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSessionRequest $request, StoreSessionAction $action): JsonResponse
    {
        $session = $action->execute($request->user(), $request->getData());

        return response()->json(['session' => $session], 201);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('session::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('session::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
