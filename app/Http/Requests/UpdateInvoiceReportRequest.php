<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceReportRequest extends FormRequest
{
     /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Prepare data before validation
     */
    protected function prepareForValidation(): void
    {
        $moneyFields = [
            'total_amount',
            'cash',
            'awaiting_invoice',
            'cheque',
            'credit',
            'pos',
            'bank_transfer',
            'return_outward',
            'invoice_to_invoice',
            'add_return_inward',
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

    /**
     * Validation rules
     */
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

            'awaiting_invoice' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'cheque' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'credit' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'pos' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'bank_transfer' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'return_outward' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'invoice_to_invoice' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'cheque_details' => [
                'nullable',
                'string',
            ],

            'pos_details' => [
                'nullable',
                'string',
            ],

            'bank_transfer_details' => [
                'nullable',
                'string',
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

    /**
     * Custom messages
     */
    public function messages(): array
    {
        return [
            'total_amount.required' =>
                'Total amount is required.',

            'shift.required' =>
                'Shift is required.',

            'date.required' =>
                'Date is required.',
        ];
    }
}
