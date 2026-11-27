<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EscalateCustomerComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && in_array($this->user()->role, ['cs', 'customer_service'], true);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'escalation_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'escalation_reason.required' => 'Alasan eskalasi ke Admin wajib diisi.',
            'escalation_reason.min' => 'Alasan eskalasi minimal 5 karakter.',
            'escalation_reason.max' => 'Alasan eskalasi maksimal 1000 karakter.',
        ];
    }
}
