<?php

namespace App\Http\Requests;

use App\Models\CustomerComplaint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerComplaintRequest extends FormRequest
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
            'booking_code' => ['nullable', 'string', 'max:100'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'category' => ['required', 'string', Rule::in(array_keys(CustomerComplaint::CATEGORIES))],
            'description' => ['required', 'string', 'min:5', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.required' => 'Kategori kendala wajib dipilih.',
            'category.in' => 'Kategori kendala tidak valid.',
            'description.required' => 'Deskripsi atau kronologi kendala wajib diisi.',
            'description.min' => 'Deskripsi kendala minimal 5 karakter.',
            'description.max' => 'Deskripsi kendala maksimal 2000 karakter.',
        ];
    }
}
