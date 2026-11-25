<?php

namespace App\Services;

use App\Models\Section;
use App\Models\Student;
use Illuminate\Support\Collection;

class ReportCardService
{
    /** Lowest general average that counts as PASSED. */
    public const PASSING_GRADE = 75;

    /**
     * General average: plain average of every grade that is not NULL,
     * rounded to 2 decimals. Null when the student has no usable grades.
     */
    public function generalAverage(Student $student): ?float
    {
        $student->loadMissing('grades');

        $scores = $student->grades
            ->pluck('grade')
            ->filter(fn ($grade) => $grade !== null)
            ->map(fn ($grade) => (float) $grade);

        if ($scores->isEmpty()) {
            return null;
        }

        return round($scores->avg(), 2);
    }

    /**
     * Remark rules, in order:
     * no grades at all -> NO GRADES, any NULL grade -> INC,
     * average at or above the passing grade -> PASSED, otherwise FAILED.
     */
    public function remark(Student $student): string
    {
        $student->loadMissing('grades');

        if ($student->grades->isEmpty()) {
            return 'NO GRADES';
        }

        if ($student->grades->contains(fn ($grade) => $grade->grade === null)) {
            return 'INC';
        }

        return $this->generalAverage($student) >= self::PASSING_GRADE ? 'PASSED' : 'FAILED';
    }

    /**
     * One row per subject: subject, sem1, sem2 and final.
     * A missing semester row counts as null, and final is null if either is missing.
     *
     * @return array<int, array{subject: \App\Models\Subject, sem1: ?float, sem2: ?float, final: ?float}>
     */
    public function rows(Student $student): array
    {
        $student->loadMissing('grades.subject');

        return $student->grades
            ->groupBy('subject_id')
            ->map(function (Collection $subjectGrades) {
                $sem1 = $this->semesterGrade($subjectGrades, 1);
                $sem2 = $this->semesterGrade($subjectGrades, 2);

                return [
                    'subject' => $subjectGrades->first()->subject,
                    'sem1' => $sem1,
                    'sem2' => $sem2,
                    'final' => ($sem1 === null || $sem2 === null) ? null : round(($sem1 + $sem2) / 2, 2),
                ];
            })
            ->sortBy(fn (array $row) => $row['subject']->name)
            ->values()
            ->all();
    }

    /**
     * Everything one report card page needs.
     *
     * @return array{student: Student, rows: array, average: ?float, remark: string}
     */
    public function card(Student $student): array
    {
        $student->load(['section.adviser', 'grades.subject']);

        return [
            'student' => $student,
            'rows' => $this->rows($student),
            'average' => $this->generalAverage($student),
            'remark' => $this->remark($student),
        ];
    }

    /**
     * Every student of a section, sorted by last name, with average and remark.
     * Students and all their grades are loaded up front (no query per student).
     *
     * @return array<int, array{student: Student, average: ?float, remark: string}>
     */
    public function classList(Section $section): array
    {
        $section->load('students.grades');

        return $section->students
            ->sortBy([['last_name', 'asc'], ['first_name', 'asc']])
            ->map(fn (Student $student) => [
                'student' => $student,
                'average' => $this->generalAverage($student),
                'remark' => $this->remark($student),
            ])
            ->values()
            ->all();
    }

    /** The grade for one semester of one subject, or null if missing or NULL. */
    private function semesterGrade(Collection $subjectGrades, int $semester): ?float
    {
        $grade = $subjectGrades->firstWhere('semester', $semester)?->grade;

        return $grade === null ? null : (float) $grade;
    }
}
