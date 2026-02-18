<?php

use App\Models\Idea;
use App\Models\User;

it('edit an existing idea', function () {
    $this->actingAs($user = User::factory()->create());

    $idea = Idea::factory()->for($user)->create();

    visit(route('ideas.show', $idea))
        ->click('@edit-idea-button')
        ->fill('title', 'Some Example Title')
        ->click('@button-status-completed')
        ->fill('description', 'An example description')
        ->fill('new-link', 'https://laracasts.com')
        ->click('@submit-new-link-button')
        ->fill('@new-step', 'Do a thing')
        ->click('@submit-new-step-button')
        ->click('@submit-idea-form')
        ->assertRoute('ideas.show', [$idea]);

    expect($idea = $user->ideas()->first())->toMatchArray([
        'title' => 'Some Example Title',
        'status' => 'completed',
        'description' => 'An example description',
        'links' => [$idea->links[0], 'https://laracasts.com'],
    ])
        ->and($idea->steps)->toHaveCount(1);

});
