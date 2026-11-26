<?php

use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function makeTeacher(string $email): Teacher
{
    $teacher = new Teacher;
    $teacher->first_name = 'Ana';
    $teacher->last_name = 'Cruz';
    $teacher->email = $email;
    $teacher->save();

    return $teacher;
}

function makeSection(string $name, ?Teacher $adviser, int $students = 0): Section
{
    $section = new Section;
    $section->name = $name;
    $section->grade_level = 10;
    $section->adviser_id = $adviser?->id;
    $section->save();

    for ($i = 0; $i < $students; $i++) {
        $student = new Student;
        $student->lrn = str_pad($section->id * 100 + $i, 12, '0', STR_PAD_LEFT);
        $student->first_name = 'Juan';
        $student->last_name = 'Santos';
        $student->section_id = $section->id;
        $student->save();
    }

    return $section;
}

function makeUser(string $role = 'teacher', ?Teacher $teacher = null): User
{
    $user = User::factory()->create();
    $user->role = $role;
    $user->teacher_id = $teacher?->id;
    $user->save();

    return $user;
}
