<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs in with correct credentials and returns a token and name', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'secret123',
        'device_name' => 'phone',
    ]);

    $response->assertStatus(201)
        ->assertJson(['name' => $user->name])
        ->assertJsonStructure(['token', 'name']);

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'name' => 'phone',
    ]);
});

it('rejects login with a wrong email with a generic message', function () {
    User::factory()->create(['password' => bcrypt('secret123')]);

    $response = $this->postJson('/api/login', [
        'email' => 'nobody@example.com',
        'password' => 'secret123',
        'device_name' => 'phone',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Wrong email or password.']);
});

it('rejects login with a wrong password with a generic message', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
        'device_name' => 'phone',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Wrong email or password.']);
});

it('requires email, password and device_name to login', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password', 'device_name']);
});

it('limits login attempts to 5 per minute', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'phone',
        ])->assertStatus(401);
    }

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
        'device_name' => 'phone',
    ])->assertStatus(429);
});

it('rejects /api/me without a token', function () {
    $this->getJson('/api/me')->assertStatus(401);
});

it('returns the authenticated user on /api/me', function () {
    $user = User::factory()->create()->refresh();
    $token = $user->createToken('phone')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/me');

    $response->assertStatus(200)
        ->assertJson([
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
});

it('logs out and deletes the current token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('phone')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/logout');

    $response->assertStatus(204);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('rejects /api/logout without a token', function () {
    $this->postJson('/api/logout')->assertStatus(401);
});
