<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateIdea;
use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\IdeaStatus;
use App\Models\Idea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class IdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'status' => ['nullable', Rule::enum(IdeaStatus::class)],
        ]);

        $ideas = $user
            ->ideas()
            ->when(in_array($request->status, IdeaStatus::values()),
                fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->get();

        return view('ideas.index', [
            'ideas' => $ideas,
            'statusCounts' => Idea::statusCount(Auth::user()),
            'statuses' => IdeaStatus::cases(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    //    public function create()
    //    {
    //        return view('ideas.create');
    //    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIdeaRequest $request): RedirectResponse
    {
        (new CreateIdea)->handle($request->safe()->all());

        return to_route('ideas.index')->with('success', 'Idea created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Idea $idea)
    {
        return view('ideas.show', [
            'idea' => $idea,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idea $idea): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIdeaRequest $request, Idea $idea): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea): RedirectResponse
    {

        $idea->delete();

        return to_route('ideas.index')->with('success', 'Idea deleted.');
    }
}
