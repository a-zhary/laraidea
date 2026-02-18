<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateIdea;
use App\Actions\UpdateIdea;
use App\Http\Requests\IdeaRequest;
use App\IdeaStatus;
use App\Models\Idea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
    public function store(IdeaRequest $request, CreateIdea $action): RedirectResponse
    {
        $action->handle($request->safe()->all());

        return to_route('ideas.index')->with('success', 'Idea created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Idea $idea)
    {
        Gate::authorize('work-with', $idea);

        return view('ideas.show', [
            'idea' => $idea,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idea $idea): void
    {
        Gate::authorize('work-with', $idea);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IdeaRequest $request, Idea $idea, UpdateIdea $action)
    {
        Gate::authorize('work-with', $idea);

        $action->handle($request->safe()->all(), $idea);

        return back()->with('success', 'Idea updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea): RedirectResponse
    {
        Gate::authorize('work-with', $idea);

        $idea->delete();

        return to_route('ideas.index')->with('success', 'Idea deleted.');
    }
}
