<?php

it('register a user', function () {
    visit('/register')
        ->fill('name', 'John Doe')
        ->fill('email', 'john@test.test')
        ->fill('password', 'password123')
        ->press('Create Account')
        ->assertPathIs('/');

    $this->assertAuthenticated();

    expect(Auth::user())->toMatchArray([
        'name' => 'John Doe',
        'email' => 'john@test.test'
    ]);
});
