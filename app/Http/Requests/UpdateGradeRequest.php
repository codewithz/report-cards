<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('grade'));
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        // 'present' means the field must be sent. null is allowed and means INC.
        return [
            'grade' => ['present', 'nullable', 'numeric', 'between:60,100', 'decimal:0,2'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'grade.present' => 'Send a grade, or null to mark it INC.',
            'grade.numeric' => 'The grade must be a number, or null for INC.',
            'grade.between' => 'The grade must be from 60 to 100.',
            'grade.decimal' => 'The grade can have at most 2 decimal places.',
        ];
    }
}
