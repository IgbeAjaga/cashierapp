<div class="row g-2">

    <!-- Invoice Number -->
    <div class="col-md-3">
    <label class="form-label">Invoice Number <span class="text-danger">*</span></label>
    <input
        type="text"
        name="invoice_number"
        value="{{ old('invoice_number', $invoiceReport->invoice_number ?? '') }}"
        class="form-control @error('invoice_number') is-invalid @enderror"
        placeholder="Enter invoice number"
        required
    >
    @error('invoice_number')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

    <!-- Customer -->
    <div class="col-md-5">
        <label class="form-label">
            Customer
        </label>

        <input
            type="text"
            name="customer"
            value="{{ old('customer', $invoiceReport->customer ?? '') }}"
            class="form-control"
        >
    </div>

    <!-- Date -->
    <div class="col-md-2">
        <label class="form-label">
            Date
        </label>

        <input
            type="date"
            name="date"
            value="{{ old('date', isset($invoiceReport) ? $invoiceReport->date->format('Y-m-d') : now()->format('Y-m-d')) }}"
            class="form-control"
            required
        >
    </div>

    <!-- Shift -->
    <div class="col-md-2">
        <label class="form-label">
            Shift
        </label>

        <select
            name="shift"
            class="form-select"
            required
        >
            <option value="morning"
                {{ old('shift', $invoiceReport->shift ?? '') == 'morning' ? 'selected' : '' }}>
                Morning
            </option>

            <option value="afternoon"
                {{ old('shift', $invoiceReport->shift ?? '') == 'afternoon' ? 'selected' : '' }}>
                Afternoon
            </option>

            <option value="night"
                {{ old('shift', $invoiceReport->shift ?? '') == 'night' ? 'selected' : '' }}>
                Night
            </option>
        </select>
    </div>

    <!-- Payment Note -->
    <div class="col-md-12">
        <label class="form-label">
            Payment Note
        </label>

        <textarea
            name="payment_note"
            rows="2"
            class="form-control"
        >{{ old('payment_note', $invoiceReport->payment_note ?? '') }}</textarea>
    </div>

</div>

<hr>

<h5 class="mb-1">
    Payment Section
</h5>

<div class="row g-2">

    @php
        $fields = [
            'total_amount'      => 'Total Amount',
            'cash'              => 'Cash',
            'awaiting_invoice'  => 'Awaiting Invoice',
            'cheque'            => 'Cheque',
            'credit'            => 'Credit',
            'pos'               => 'POS',
            'bank_transfer'     => 'Bank Transfer',
            'return_outward'    => 'Return Outward',
            'invoice_to_invoice'=> 'Invoice To Invoice',
            'return_inward' => 'Return Inward',
            'less_credit'       => 'Less Credit',
            'balance_to_use'    => 'Balance To Use',
        ];
    @endphp

   @foreach($fields as $name => $label)

    @php
        $value = old($name, $invoiceReport->$name ?? '');

        // Treat 0, 0.00, null as blank
        $displayValue = (
            $value === null ||
            $value === '' ||
            (float)$value == 0
        ) ? '' : $value;
    @endphp

    <div class="col-md-3">

        <label class="form-label">
            {{ $label }}
        </label>

        <!-- DISPLAY FIELD -->
        <input
            type="text"
            id="{{ $name }}_display"
            value="{{ $displayValue }}"
            class="form-control text-end calc-field"
            placeholder="0.00"
            autocomplete="off"
        >

        <!-- REAL VALUE -->
        <input
            type="hidden"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $value ?: 0 }}"
        >

    </div>

@endforeach

</div>

<hr>

<h5 class="mb-1">
    Details
</h5>

<div class="row g-2">

    <!-- Cheque -->
    <div class="col-md-4">

        <label class="form-label">
            Cheque Details
        </label>

        <textarea
            name="cheque_details"
            rows="2"
            class="form-control"
        >{{ old('cheque_details', $invoiceReport->cheque_details ?? '') }}</textarea>

    </div>

    <!-- POS -->
    <div class="col-md-4">

        <label class="form-label">
            POS Details
        </label>

        <textarea
            name="pos_details"
            rows="2"
            class="form-control"
        >{{ old('pos_details', $invoiceReport->pos_details ?? '') }}</textarea>

    </div>

    <!-- Transfer -->
    <div class="col-md-4">

        <label class="form-label">
            Bank Transfer Details
        </label>

        <textarea
            name="bank_transfer_details"
            rows="2"
            class="form-control"
        >{{ old('bank_transfer_details', $invoiceReport->bank_transfer_details ?? '') }}</textarea>

    </div>

</div>

<!-- BUTTONS -->
<div class="mt-3 d-flex gap-1">

    <a
        href="{{ route('invoice-reports.index') }}"
        class="btn btn-secondary"
    >
        Back
    </a>

    <button
        type="submit"
        class="btn btn-primary"
    >
        Save Record
    </button>

</div>

<script>
/*
|--------------------------------------------------------------------------
| MONEY FIELD FORMATTER
|--------------------------------------------------------------------------
| - Adds commas automatically
| - DOES NOT force .00
| - Allows typing decimals naturally
| - Preserves cursor position
|--------------------------------------------------------------------------
*/

const moneyFields = [
    'total_amount',
    'cash',
    'awaiting_invoice',
    'cheque',
    'credit',
    'pos',
    'bank_transfer',
    'return_outward',
    'invoice_to_invoice',
    'return_inward',
    'less_credit',
    'balance_to_use'
];

moneyFields.forEach(field => {

    const display = document.getElementById(field + '_display');
    const hidden  = document.getElementById(field);

    if (!display || !hidden) return;

    /**
     * Format number with commas only
     */
    function formatNumber(value)
{
    if (!value) return '';

    // Preserve trailing decimal point while typing
    const endsWithDot = value.endsWith('.');

    let parts = value.split('.');

    parts[0] = parts[0].replace(
        /\B(?=(\d{3})+(?!\d))/g,
        ','
    );

    let result = parts.join('.');

    if (endsWithDot && !result.endsWith('.')) {
        result += '.';
    }

    return result;
}

    /**
     * Initial page load formatting
     */
    if (display.value) {

    let raw = display.value
        .replace(/,/g, '')
        .replace(/[^\d.]/g, '');

    // Blank out zero values
    if (parseFloat(raw) === 0) {
        display.value = '';
        hidden.value = '';
    } else {

        // Remove .00 only on load
        if (raw.match(/^\d+\.00$/)) {
            raw = parseInt(raw).toString();
        }

        hidden.value = raw;
        display.value = formatNumber(raw);
    }
}

    /**
     * Live formatting
     */
    display.addEventListener('input', function () {

        let cursorPosition = this.selectionStart;
        let previousLength = this.value.length;

        // Remove commas
        let raw = this.value.replace(/,/g, '');

        // Allow only numbers + single decimal
        raw = raw.replace(/[^\d.]/g, '');

        const parts = raw.split('.');

        if (parts.length > 2) {
            raw =
                parts[0] +
                '.' +
                parts.slice(1).join('');
        }

        hidden.value = raw;

        // Format visually
        this.value = formatNumber(raw);

        // Restore cursor position
        let newLength = this.value.length;
        let difference = newLength - previousLength;

        this.setSelectionRange(
            cursorPosition + difference,
            cursorPosition + difference
        );
    });

});
</script>