@extends('layouts.app')

@section('title', 'Report Card - ' . $student->fullName())

@section('content')
    <h1 class="mb-1 text-2xl font-bold">{{ $student->fullName() }}</h1>
    <p class="mb-4 text-sm text-slate-600">
        LRN {{ $student->lrn }}
        &middot; Grade {{ $student->section->grade_level }} - {{ $student->section->name }}
        &middot; Adviser: {{ $student->section->adviser?->fullName() ?? '—' }}
    </p>

    <table class="w-full">
        <thead>
            <tr>
                <th>Subject</th>
                <th class="text-right">Sem 1</th>
                <th class="text-right">Sem 2</th>
                <th class="text-right">Final</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['subject']->name }}</td>
                    <td class="text-right">{{ $row['sem1'] === null ? '—' : number_format($row['sem1'], 2) }}</td>
                    <td class="text-right">{{ $row['sem2'] === null ? '—' : number_format($row['sem2'], 2) }}</td>
                    <td class="text-right font-semibold">{{ $row['final'] === null ? '—' : number_format($row['final'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-slate-500">No grades recorded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="summary mt-4">
        General average:
        <strong>{{ $average === null ? '—' : number_format($average, 2) }}</strong>
        &middot;
        Remark: <span class="{{ \Illuminate\Support\Str::slug($remark) }}">{{ $remark }}</span>
    </p>
@endsection
