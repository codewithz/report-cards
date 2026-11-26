<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportCardResource;
use App\Models\Student;
use App\Services\ReportCardService;
use Illuminate\Support\Facades\Gate;

class ReportCardController extends Controller
{
    public function __construct(private ReportCardService $reportCards)
    {
    }

    public function show(Student $student)
    {
        Gate::authorize('view', $student->section);

        return new ReportCardResource($this->reportCards->card($student));
    }
}
