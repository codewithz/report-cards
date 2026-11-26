<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $sections = Section::visibleTo($request->user())
            ->withCount('students')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return SectionResource::collection($sections);
    }
}
