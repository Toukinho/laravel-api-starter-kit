<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

it('logs in and returns a token', function (): void {
    $user = User::factory()->create();

    $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password'])
        ->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'email']]);
});

it('rejects bad credentials', function (): void {
    $user = User::factory()->create();

    $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'wrong'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('returns the current user', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', $user->email);
});

it('rejects unauthenticated me with json 401', function (): void {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});

it('revokes token on logout', function (): void {
    $token = User::factory()->create()->createToken('t')->plainTextToken;

    $this->withToken($token)->postJson('/api/v1/auth/logout')->assertNoContent();

    expect(PersonalAccessToken::count())->toBe(0);
});
