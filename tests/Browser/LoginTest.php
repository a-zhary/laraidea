<?php

use App\Models\User;

it('login a user', function () {
    $user = User::factory()->create();

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->submit()
        ->assertRoute('ideas.index');

    $this->assertAuthenticated();
});

it('logout a user', function () {
    $user = User::factory()->create();

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->submit()
        ->assertRoute('ideas.index')
        ->click('Logout');

    $this->assertGuest();
});
