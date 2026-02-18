<?php

use App\Models\User;
use App\Notifications\EmailChanged;

it('require authentication', function () {
    $this->get('/profile/edit')->assertRedirect('/login');
});

it('edits profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    visit(route('profile.edit'))
        ->assertValue('name', $user->name)
        ->fill('name', 'vasya')
        ->fill('email', 'test@test.test')
        ->click('Edit')
        ->assertSee('Your profile has been updated.');

    expect($user->fresh())->toMatchArray([
        'name' => 'vasya',
        'email' => 'test@test.test',
    ]);
});

it('notify if original email is changed', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Notification::fake();

    $originalEmail = $user->email;

    visit(route('profile.edit'))
        ->assertValue('name', $user->name)
        ->fill('email', 'test@test.test')
        ->click('Edit')
        ->assertSee('Your profile has been updated.');

    Notification::assertSentOnDemand(EmailChanged::class, fn (EmailChanged $notification, $routes, $notifiable) => $notifiable->routes['mail'] === $originalEmail);

});
