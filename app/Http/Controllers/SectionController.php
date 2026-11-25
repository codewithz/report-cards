<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Services\ReportCardService;

class SectionController extends Controller
{
    public function __construct(private ReportCardService $reportCards)
    {
    }

    public function index()
    {
        $sections = Section::with('adviser')
            ->withCount('students')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return view('sections.index', ['sections' => $sections]);
    }

    public function show(Section $section)
    {
        $section->load('adviser');

        return view('sections.show', [
            'section' => $section,
            'summaries' => $this->reportCards->classList($section),
        ]);
    }
}
