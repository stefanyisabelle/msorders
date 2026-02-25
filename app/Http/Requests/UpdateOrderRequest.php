<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $order = \App\Models\Order::findOrFail($this->route('id'));
        return Gate::allows('update', $order);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'sometimes|required|string|max:255',
            'destination' => 'sometimes|required|string|max:255',
            'departure_date' => 'sometimes|required|date|after_or_equal:today',
            'return_date' => 'sometimes|required|date|after_or_equal:departure_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'The customer name field is required.',
            'destination.required' => 'The destination field is required.',
            'departure_date.required' => 'The departure date field is required.',
            'departure_date.after_or_equal' => 'The departure date cannot be before today.',
            'return_date.required' => 'The return date field is required.',
            'return_date.after_or_equal' => 'The return date must be equal to or after the departure date.',
        ];
    }
}
