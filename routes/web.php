<?php

use App\Http\Controllers\IdeaController;
use App\Http\Controllers\IdeaImageController;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/ideas');

Route::middleware(['auth'])->group(function () {
    Route::get('/ideas', [IdeaController::class, 'index'])->name('ideas.index');
    Route::post('/ideas', [IdeaController::class, 'store'])->name('ideas.store');
    Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('ideas.show');
    Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])->name('ideas.destroy');
    Route::patch('/ideas/{idea}', [IdeaController::class, 'update'])->name('ideas.update');

    Route::delete('/ideas/{idea}/image', [IdeaImageController::class, 'destroy'])->name('ideas.image.destroy');

    Route::patch('/steps/{step}', [StepController::class, 'update'])->name('steps.update');

    Route::post('/logout', [SessionsController::class, 'destroy']);
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterUserController::class, 'create']);
    Route::post('/register', [RegisterUserController::class, 'store']);

    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/login', [SessionsController::class, 'store']);
});
