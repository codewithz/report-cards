@extends('layouts.app')

@section('title', 'Sections')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Sections</h1>

    <table>
        <thead>
            <tr>
                <th>Section</th>
                <th>Grade level</th>
                <th>Adviser</th>
                <th>Students</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sections as $section)
                <tr>
                    <td><a href="{{ route('sections.show', $section) }}">{{ $section->name }}</a></td>
                    <td>{{ $section->grade_level }}</td>
                    <td>{{ $section->adviser?->fullName() ?? 'No adviser' }}</td>
                    <td>{{ $section->students_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
