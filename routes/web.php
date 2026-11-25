<?php

use App\Http\Controllers\ClassListController;
use App\Http\Controllers\ReportCardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/students/{student}/report-card', [ReportCardController::class, 'show'])
    ->name('report-card.show');

Route::get('/sections/{section}/class-list', [ClassListController::class, 'show'])
    ->name('class-list.show');
