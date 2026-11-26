<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Wraps the array returned by ReportCardService::card(). No calculations here.
class ReportCardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'lrn' => $this->resource['student']->lrn,
            'section' => $this->resource['student']->section->name,
            'subjects' => array_map(fn (array $row) => [
                'subject' => $row['subject']->name,
                'sem1' => $row['sem1'],
                'sem2' => $row['sem2'],
                'final' => $row['final'],
            ], $this->resource['rows']),
            'general_average' => $this->resource['average'],
            'remark' => $this->resource['remark'],
        ];
    }
}
