<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\ReportCardService;

class ReportCardController extends Controller
{
    public function __construct(private ReportCardService $reportCards)
    {
    }

    public function show(Student $student)
    {
        return view('report-cards.show', $this->reportCards->card($student));
    }
}
