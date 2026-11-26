<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'subject_id' => $this->subject_id,
            'semester' => $this->semester,
            // MariaDB returns decimals as text, so send a number or null.
            'grade' => $this->grade === null ? null : (float) $this->grade,
        ];
    }
}
