<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller via OrderService
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:Pending,Approved,Packed,Out for Delivery,Delivered,Cancelled',
            'assigned_delivery' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Order status is required',
            'status.in' => 'Invalid status provided',
            'assigned_delivery.exists' => 'Selected delivery person does not exist',
            'notes.max' => 'Notes cannot exceed 1000 characters',
        ];
    }

    /**
     * Custom attribute names
     */
    public function attributes(): array
    {
        return [
            'status' => 'Order Status',
            'assigned_delivery' => 'Delivery Person',
            'notes' => 'Additional Notes',
        ];
    }
}
