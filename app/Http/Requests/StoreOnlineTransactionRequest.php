<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOnlineTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $moneyFields = [
            'total_amount',
            'cash',
            'cheque',
            'pos',
            'transfer',
            'return_inward',
            'less_credit',
            'balance_to_use',
            'difference',
        ];

        foreach ($moneyFields as $field) {
            $this->merge([
                $field => $this->$field
                    ? str_replace(',', '', $this->$field)
                    : 0
            ]);
        }
    }

    public function rules(): array
    {
        return [

            'invoice_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'customer' => [
                'nullable',
                'string',
                'max:255',
            ],

            'payment_note' => [
                'nullable',
                'string',
            ],

            'total_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'cash' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'cheque' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'cheque_details' => [
                'nullable',
                'string',
            ],

            'pos' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'transfer' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'return_inward' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'less_credit' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'balance_to_use' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'difference' => [
                'nullable',
                'numeric',
            ],

            'is_balanced' => [
                'nullable',
                'boolean',
            ],

            'date' => [
                'required',
                'date',
            ],

            'shift' => [
                'required',
                'in:morning,afternoon,night',
            ],
        ];
    }
}