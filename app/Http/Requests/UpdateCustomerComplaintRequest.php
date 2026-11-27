<?php

namespace App\Http\Requests;

use App\Models\CustomerComplaint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerComplaintRequest extends FormRequest
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
            'status' => ['required', 'string', Rule::in(array_keys(CustomerComplaint::STATUSES))],
            'follow_up_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status penanganan wajib dipilih.',
            'status.in' => 'Status penanganan tidak valid.',
            'follow_up_notes.max' => 'Catatan tindak lanjut maksimal 2000 karakter.',
        ];
    }
}
