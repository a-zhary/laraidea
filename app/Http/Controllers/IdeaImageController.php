<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class IdeaImageController extends Controller
{
    public function destroy(Idea $idea)
    {
        Gate::authorize('work-with', $idea);

        Storage::disk('public')->delete($idea->image_path);

        $idea->update(['image_path' => null]);

        return back();
    }
}
