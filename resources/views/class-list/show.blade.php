@extends('layouts.app')

@section('title', 'Class List - ' . $section->name)

@section('content')
    <h1 class="mb-1 text-2xl font-bold">Grade {{ $section->grade_level }} - {{ $section->name }}</h1>
    <p class="mb-4 text-sm text-slate-600">Class list, sorted by last name</p>

    <table class="w-full">
        <thead>
            <tr>
                <th>Student</th>
                <th class="text-right">General average</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $row)
                <tr>
                    <td>
                        <a href="{{ route('report-card.show', $row['student']) }}">{{ $row['student']->fullName() }}</a>
                    </td>
                    <td class="text-right">{{ $row['average'] === null ? '—' : number_format($row['average'], 2) }}</td>
                    <td><span class="{{ \Illuminate\Support\Str::slug($row['remark']) }}">{{ $row['remark'] }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-slate-500">No students in this section.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
