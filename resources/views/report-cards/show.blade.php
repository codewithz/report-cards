@extends('layouts.app')

@section('title', 'Report card - ' . $student->fullName())

@section('content')
    <h1 class="text-2xl font-bold mb-2">Report Card</h1>

    <div class="mb-4">
        <p><strong>Name:</strong> {{ $student->fullName() }}</p>
        <p><strong>LRN:</strong> {{ $student->lrn }}</p>
        <p><strong>Grade and section:</strong> Grade {{ $student->section->grade_level }} - {{ $student->section->name }}</p>
        <p><strong>Adviser:</strong> {{ $student->section->adviser?->fullName() ?? 'No adviser' }}</p>
    </div>

    @if (count($rows) === 0)
        <p>No grades recorded yet</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Sem 1</th>
                    <th>Sem 2</th>
                    <th>Final</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td>{{ $row['subject']->name }}</td>
                        <td>{{ $row['sem1'] === null ? 'INC' : number_format($row['sem1'], 2) }}</td>
                        <td>{{ $row['sem2'] === null ? 'INC' : number_format($row['sem2'], 2) }}</td>
                        <td>{{ $row['final'] === null ? 'INC' : number_format($row['final'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p class="summary mt-4">
        General average: <strong>{{ $average === null ? '-' : number_format($average, 2) }}</strong>
        &middot;
        Remark: <span class="{{ \Illuminate\Support\Str::slug($remark) }}">{{ $remark }}</span>
    </p>

    <p class="mt-4"><a href="{{ route('sections.show', $student->section) }}">&larr; Back to {{ $student->section->name }}</a></p>
@endsection
