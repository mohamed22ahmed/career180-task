<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);
});

test('users can not register with invalid data', function () {
    $response = $this->post(route('register.store'), [
        'name' => '',
        'email' => 'invalid-email',
        'password' => 'short',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'password']);

    $this->assertGuest();
});

test('users can not register with existing email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);

    $this->assertGuest();
});
