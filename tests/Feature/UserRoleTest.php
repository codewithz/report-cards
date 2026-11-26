<?php

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('gives a new user the teacher role', function () {
    $user = User::factory()->create()->refresh();

    expect($user->role)->toBe('teacher')
        ->and($user->isAdmin())->toBeFalse();
});

it('treats only the admin role as admin', function () {
    $user = User::factory()->create();
    $user->role = 'admin';
    $user->save();

    expect($user->refresh()->isAdmin())->toBeTrue();
});

it('links a user to a teacher', function () {
    $teacher = new Teacher;
    $teacher->first_name = 'Ana';
    $teacher->last_name = 'Cruz';
    $teacher->email = 'ana.cruz@example.com';
    $teacher->save();

    $user = User::factory()->create();
    $user->teacher_id = $teacher->id;
    $user->save();

    expect($user->refresh()->teacher->is($teacher))->toBeTrue();
});

it('ignores role and teacher_id in mass assignment', function () {
    $user = User::factory()->create();

    $user->update(['name' => 'New Name', 'role' => 'admin', 'teacher_id' => 1]);
    $user->refresh();

    expect($user->name)->toBe('New Name')
        ->and($user->role)->toBe('teacher')
        ->and($user->teacher_id)->toBeNull();
});
