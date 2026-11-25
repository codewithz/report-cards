<?php

use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/sections');

Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
Route::get('/sections/{section}', [SectionController::class, 'show'])->name('sections.show');

Route::get('/students/{student}', [ReportCardController::class, 'show'])->name('students.show');
