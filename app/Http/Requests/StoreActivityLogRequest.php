<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Use the existing isSuperAdmin() method from the User model.
        return $this->user() && $this->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|integer|exists:users,id',
            'action' => 'required|string|max:255',
            'subject_id' => 'required|integer',
            'subject_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'before' => 'nullable|array',
            'after' => 'nullable|array',
        ];
    }
}
