@extends('layouts.app')

@section('title', $section->name)

@section('content')
    <p class="mb-2"><a href="{{ route('sections.index') }}">&larr; All sections</a></p>

    <h1 class="text-2xl font-bold">Grade {{ $section->grade_level }} - {{ $section->name }}</h1>
    <p class="mb-4">Adviser: {{ $section->adviser?->fullName() ?? 'No adviser' }}</p>

    <table>
        <thead>
            <tr>
                <th>LRN</th>
                <th>Name</th>
                <th>General average</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summaries as $summary)
                <tr>
                    <td>{{ $summary['student']->lrn }}</td>
                    <td>
                        <a href="{{ route('students.show', $summary['student']) }}">{{ $summary['student']->fullName() }}</a>
                    </td>
                    <td>{{ $summary['average'] === null ? '-' : number_format($summary['average'], 2) }}</td>
                    <td><span class="{{ \Illuminate\Support\Str::slug($summary['remark']) }}">{{ $summary['remark'] }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
