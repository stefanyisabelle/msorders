<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', \App\Models\Order::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after_or_equal:departure_date',
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
            'customer_name.max' => 'The customer name may not be greater than 255 characters.',
            'destination.required' => 'The destination field is required.',
            'destination.max' => 'The destination may not be greater than 255 characters.',
            'departure_date.required' => 'The departure date field is required.',
            'departure_date.date' => 'The departure date must be a valid date.',
            'departure_date.after_or_equal' => 'The departure date must be a date after or equal to today.',
            'return_date.required' => 'The return date field is required.',
            'return_date.date' => 'The return date must be a valid date.',
            'return_date.after_or_equal' => 'The return date must be a date after or equal to the departure date.',
        ];
    }
}
