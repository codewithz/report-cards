<?php

use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->adviser = makeTeacher('ana@example.com');
    $this->otherTeacher = makeTeacher('ben@example.com');

    $this->section = makeSection('Sampaguita', $this->adviser, 1);
    $this->student = $this->section->students()->first();
    $this->url = "/api/students/{$this->student->id}/report-card";

    // One grade row per semester given: [1 => 80.5, 2 => null].
    $this->addGrades = function (string $name, array $semesters) {
        $subject = new Subject;
        $subject->code = substr($name, 0, 10);
        $subject->name = $name;
        $subject->units = 1.0;
        $subject->save();

        foreach ($semesters as $semester => $score) {
            $grade = new Grade;
            $grade->student_id = $this->student->id;
            $grade->subject_id = $subject->id;
            $grade->teacher_id = $this->otherTeacher->id;
            $grade->school_year = '2025-2026';
            $grade->semester = $semester;
            $grade->grade = $score;
            $grade->save();
        }
    };
});

it('rejects the report card without a token', function () {
    $this->getJson($this->url)->assertStatus(401);
});

it('shows the admin a report card with only the listed fields', function () {
    ($this->addGrades)('Science', [1 => 80.25, 2 => 85.75]);
    ($this->addGrades)('English', [1 => 90.50, 2 => 91.50]);
    Sanctum::actingAs(makeUser('admin'));

    $this->getJson($this->url)
        ->assertStatus(200)
        ->assertExactJson(['data' => [
            'lrn' => $this->student->lrn,
            'section' => 'Sampaguita',
            'subjects' => [
                ['subject' => 'English', 'sem1' => 90.5, 'sem2' => 91.5, 'final' => 91],
                ['subject' => 'Science', 'sem1' => 80.25, 'sem2' => 85.75, 'final' => 83],
            ],
            'general_average' => 87,
            'remark' => 'PASSED',
        ]]);
});

it('shows the adviser the report card', function () {
    Sanctum::actingAs(makeUser('teacher', $this->adviser));

    $this->getJson($this->url)
        ->assertStatus(200)
        ->assertJsonPath('data.lrn', $this->student->lrn);
});

it('forbids a teacher who does not advise the section, even one who grades the student', function () {
    ($this->addGrades)('Science', [1 => 80.25]);
    Sanctum::actingAs(makeUser('teacher', $this->otherTeacher));

    $this->getJson($this->url)->assertStatus(403);
});

it('forbids a user with no teacher link', function () {
    Sanctum::actingAs(makeUser());

    $this->getJson($this->url)->assertStatus(403);
});

it('returns 404 for a student who does not exist', function () {
    Sanctum::actingAs(makeUser('admin'));

    $this->getJson('/api/students/999999/report-card')->assertStatus(404);
});

it('sends null and INC when a grade is missing', function () {
    ($this->addGrades)('Science', [1 => 80.00, 2 => null]);
    Sanctum::actingAs(makeUser('admin'));

    $this->getJson($this->url)
        ->assertStatus(200)
        ->assertJsonPath('data.subjects.0.sem2', null)
        ->assertJsonPath('data.subjects.0.final', null)
        ->assertJsonPath('data.general_average', 80)
        ->assertJsonPath('data.remark', 'INC');
});

it('sends NO GRADES for a student without grades', function () {
    Sanctum::actingAs(makeUser('admin'));

    $this->getJson($this->url)
        ->assertStatus(200)
        ->assertJsonPath('data.subjects', [])
        ->assertJsonPath('data.general_average', null)
        ->assertJsonPath('data.remark', 'NO GRADES');
});
