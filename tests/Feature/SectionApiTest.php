<?php

use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->ana = makeTeacher('ana@example.com');
    $ben = makeTeacher('ben@example.com');

    $this->own = makeSection('Sampaguita', $this->ana, 2);
    $this->other = makeSection('Narra', $ben, 1);
    $this->unadvised = makeSection('Ilang-Ilang', null);
});

it('rejects /api/sections without a token', function () {
    $this->getJson('/api/sections')->assertStatus(401);
});

it('shows the admin every section with only the listed fields', function () {
    Sanctum::actingAs(makeUser('admin'));

    $this->getJson('/api/sections')
        ->assertStatus(200)
        ->assertExactJson(['data' => [
            ['id' => $this->unadvised->id, 'name' => 'Ilang-Ilang', 'grade_level' => 10, 'students_count' => 0],
            ['id' => $this->other->id, 'name' => 'Narra', 'grade_level' => 10, 'students_count' => 1],
            ['id' => $this->own->id, 'name' => 'Sampaguita', 'grade_level' => 10, 'students_count' => 2],
        ]]);
});

it('shows a teacher only the sections they advise', function () {
    Sanctum::actingAs(makeUser('teacher', $this->ana));

    $this->getJson('/api/sections')
        ->assertStatus(200)
        ->assertExactJson(['data' => [
            ['id' => $this->own->id, 'name' => 'Sampaguita', 'grade_level' => 10, 'students_count' => 2],
        ]]);
});

it('shows nothing to a user with no teacher link', function () {
    Sanctum::actingAs(makeUser());

    $this->getJson('/api/sections')
        ->assertStatus(200)
        ->assertExactJson(['data' => []]);
});

it('lets the admin view any section', function () {
    $admin = makeUser('admin');

    foreach ([$this->own, $this->other, $this->unadvised] as $section) {
        expect(Gate::forUser($admin)->allows('view', $section))->toBeTrue();
    }
});

it('lets a teacher view only an advised section', function () {
    $gate = Gate::forUser(makeUser('teacher', $this->ana));

    expect($gate->allows('view', $this->own))->toBeTrue()
        ->and($gate->allows('view', $this->other))->toBeFalse()
        ->and($gate->allows('view', $this->unadvised))->toBeFalse();
});

it('lets a user with no teacher link view no section', function () {
    $gate = Gate::forUser(makeUser());

    foreach ([$this->own, $this->other, $this->unadvised] as $section) {
        expect($gate->allows('view', $section))->toBeFalse();
    }
});
