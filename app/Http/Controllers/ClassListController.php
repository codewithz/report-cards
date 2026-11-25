<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Services\ReportCardService;

class ClassListController extends Controller
{
    public function __construct(private ReportCardService $reportCards)
    {
    }

    public function show(Section $section)
    {
        return view('class-list.show', [
            'section' => $section,
            'students' => $this->reportCards->classList($section),
        ]);
    }
}
