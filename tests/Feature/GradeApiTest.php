<?php

use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->adviser = makeTeacher('ana@example.com');
    $this->subjectTeacher = makeTeacher('ben@example.com');

    $section = makeSection('Sampaguita', $this->adviser, 1);

    $subject = new Subject;
    $subject->code = 'SCI10';
    $subject->name = 'Science 10';
    $subject->units = 1.5;
    $subject->save();

    $this->grade = new Grade;
    $this->grade->student_id = $section->students()->first()->id;
    $this->grade->subject_id = $subject->id;
    $this->grade->teacher_id = $this->subjectTeacher->id;
    $this->grade->school_year = '2025-2026';
    $this->grade->semester = 1;
    $this->grade->grade = 80;
    $this->grade->save();

    $this->url = "/api/grades/{$this->grade->id}";

    $this->storedGrade = function (): ?float {
        $value = $this->grade->fresh()->grade;

        return $value === null ? null : (float) $value;
    };
});

it('rejects a grade change without a token', function () {
    $this->putJson($this->url, ['grade' => 90])->assertStatus(401);

    expect(($this->storedGrade)())->toBe(80.0);
});

it('returns 404 for a grade that does not exist', function () {
    Sanctum::actingAs(makeUser('admin'));

    $this->putJson('/api/grades/999999', ['grade' => 90])->assertStatus(404);
});

it('lets the admin change a grade and returns only the listed fields', function () {
    Sanctum::actingAs(makeUser('admin'));

    $this->putJson($this->url, ['grade' => 90.5])
        ->assertStatus(200)
        ->assertExactJson(['data' => [
            'id' => $this->grade->id,
            'student_id' => $this->grade->student_id,
            'subject_id' => $this->grade->subject_id,
            'semester' => 1,
            'grade' => 90.5,
        ]]);

    expect(($this->storedGrade)())->toBe(90.5);
});

it('lets the teacher of the grade change it', function () {
    Sanctum::actingAs(makeUser('teacher', $this->subjectTeacher));

    $this->putJson($this->url, ['grade' => 95])->assertStatus(200);

    expect(($this->storedGrade)())->toBe(95.0);
});

it('forbids the section adviser who did not give the grade', function () {
    Sanctum::actingAs(makeUser('teacher', $this->adviser));

    $this->putJson($this->url, ['grade' => 90])->assertStatus(403);

    expect(($this->storedGrade)())->toBe(80.0);
});

it('forbids another teacher', function () {
    Sanctum::actingAs(makeUser('teacher', makeTeacher('cara@example.com')));

    $this->putJson($this->url, ['grade' => 90])->assertStatus(403);

    expect(($this->storedGrade)())->toBe(80.0);
});

it('forbids a user with no teacher link', function () {
    Sanctum::actingAs(makeUser());

    $this->putJson($this->url, ['grade' => 90])->assertStatus(403);

    expect(($this->storedGrade)())->toBe(80.0);
});

it('checks permission before validating', function () {
    Sanctum::actingAs(makeUser());

    $this->putJson($this->url, ['grade' => 'abc'])->assertStatus(403);
});

it('accepts grades inside the rules', function (mixed $input, float $stored) {
    Sanctum::actingAs(makeUser('admin'));

    $this->putJson($this->url, ['grade' => $input])->assertStatus(200);

    expect(($this->storedGrade)())->toBe($stored);
})->with([
    'lowest' => [60, 60.0],
    'highest' => [100, 100.0],
    'two decimals' => [88.25, 88.25],
    'numeric text' => ['75.5', 75.5],
]);

it('marks a grade INC with null or empty text', function (?string $input) {
    Sanctum::actingAs(makeUser('admin'));

    $this->putJson($this->url, ['grade' => $input])
        ->assertStatus(200)
        ->assertJsonPath('data.grade', null);

    expect(($this->storedGrade)())->toBeNull();
})->with([
    'null' => [null],
    'empty text' => [''],
]);

it('rejects bad input with a friendly message', function (array $body, string $message) {
    Sanctum::actingAs(makeUser('admin'));

    $this->putJson($this->url, $body)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['grade' => $message]);

    expect(($this->storedGrade)())->toBe(80.0);
})->with([
    'left out' => [[], 'Send a grade, or null to mark it INC.'],
    'not a number' => [['grade' => 'abc'], 'The grade must be a number, or null for INC.'],
    'too low' => [['grade' => 59.99], 'The grade must be from 60 to 100.'],
    'too high' => [['grade' => 100.01], 'The grade must be from 60 to 100.'],
    'three decimals' => [['grade' => 88.125], 'The grade can have at most 2 decimal places.'],
]);

it('ignores every field except grade', function () {
    Sanctum::actingAs(makeUser('teacher', $this->subjectTeacher));
    $before = $this->grade->fresh();

    $this->putJson($this->url, [
        'grade' => 90,
        'teacher_id' => $this->adviser->id,
        'student_id' => 999,
        'subject_id' => 999,
        'semester' => 2,
        'school_year' => '2030-2031',
    ])->assertStatus(200);

    $after = $this->grade->fresh();

    foreach (['teacher_id', 'student_id', 'subject_id', 'semester', 'school_year'] as $column) {
        expect($after->$column)->toBe($before->$column);
    }
});

it('makes only grade fillable', function () {
    expect((new Grade)->getFillable())->toBe(['grade']);
});
