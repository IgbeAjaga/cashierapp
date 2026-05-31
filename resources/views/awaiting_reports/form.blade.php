@csrf

<div class="row g-2">

    <!-- Invoice Number -->
    <div class="col-md-3">
    <label class="form-label">Invoice Number <span class="text-danger">*</span></label>
    <input
        type="text"
        name="invoice_number"
        value="{{ old('invoice_number', $awaitingReport->invoice_number ?? '') }}"
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
        <label class="form-label">Customer</label>
        <input
            type="text"
            name="customer"
            value="{{ old('customer', $awaitingReport->customer ?? '') }}"
            class="form-control @error('customer') is-invalid @enderror"
            placeholder="Enter customer name"
        >
        @error('customer')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Date -->
    <div class="col-md-2">
        <label class="form-label">Date</label>
        <input
            type="date"
            name="date"
            value="{{ old('date', isset($awaitingReport) && $awaitingReport->date ? $awaitingReport->date->format('Y-m-d') : now()->format('Y-m-d')) }}"
            class="form-control @error('date') is-invalid @enderror"
            required
        >
        @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Shift -->
    <div class="col-md-2">
        <label class="form-label">Shift</label>
        <select
            name="shift"
            class="form-select @error('shift') is-invalid @enderror"
            required
        >
            <option value="morning" {{ old('shift', $awaitingReport->shift ?? '') == 'morning' ? 'selected' : '' }}>Morning</option>
            <option value="afternoon" {{ old('shift', $awaitingReport->shift ?? '') == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
            <option value="night" {{ old('shift', $awaitingReport->shift ?? '') == 'night' ? 'selected' : '' }}>Night</option>
        </select>
        @error('shift')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Payment Note -->
    <div class="col-md-12">
        <label class="form-label">Payment Note</label>
        <textarea
            name="payment_note"
            rows="2"
            class="form-control @error('payment_note') is-invalid @enderror"
            placeholder="Enter payment note or remarks"
        >{{ old('payment_note', $awaitingReport->payment_note ?? '') }}</textarea>
        @error('payment_note')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

<hr>

<h5 class="mb-1">Payment Section</h5>

<div class="row g-2">
    @php
        $fields = [
            'total_amount'   => 'Total Amount',
            'cash'           => 'Cash',
            'cheque'         => 'Cheque',
            'pos'            => 'POS',
            'bank_transfer'  => 'Bank Transfer',
            'return_inward'  => 'Return Inward',
            'balance_to_use' => 'Balance To Use',
        ];
    @endphp

    @foreach($fields as $name => $label)

    @php
        $value = old($name, $awaitingReport->$name ?? '');

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

<div class="mt-3 d-flex gap-1">
    <a href="{{ route('awaiting-reports.index') }}" class="btn btn-secondary">Back</a>
    <button type="submit" class="btn btn-primary">Save Record</button>
</div>

<script>
/*
|--------------------------------------------------------------------------
| MONEY FIELD FORMATTER
|--------------------------------------------------------------------------
| - Adds comma formatting only
| - DOES NOT force .00
| - Allows user type decimals manually
| - Keeps cursor position stable
|--------------------------------------------------------------------------
*/

const moneyFields = [
    'total_amount',
    'cash',
    'cheque',
    'pos',
    'bank_transfer',
    'return_inward',
    'balance_to_use'
];

moneyFields.forEach(field => {

    const display = document.getElementById(field + '_display');
    const hidden  = document.getElementById(field);

    if (!display || !hidden) return;

    /*
    |--------------------------------------------------------------------------
    | Initial Load Formatting
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | On Typing
    |--------------------------------------------------------------------------
    */
    display.addEventListener('input', function () {

        let cursorPosition = this.selectionStart;
        let originalLength = this.value.length;

        /*
        |--------------------------------------------------------------------------
        | Remove commas
        |--------------------------------------------------------------------------
        */
        let raw = this.value.replace(/,/g, '');

        /*
        |--------------------------------------------------------------------------
        | Allow only digits + one decimal point
        |--------------------------------------------------------------------------
        */
        raw = raw.replace(/[^\d.]/g, '');

        /*
        |--------------------------------------------------------------------------
        | Prevent multiple decimals
        |--------------------------------------------------------------------------
        */
        const parts = raw.split('.');

        if (parts.length > 2) {
            raw = parts[0] + '.' + parts.slice(1).join('');
        }

        /*
        |--------------------------------------------------------------------------
        | Update hidden field
        |--------------------------------------------------------------------------
        */
        hidden.value = raw;

        /*
        |--------------------------------------------------------------------------
        | Format display WITHOUT forcing decimals
        |--------------------------------------------------------------------------
        */
        this.value = formatNumber(raw);

        /*
        |--------------------------------------------------------------------------
        | Maintain cursor position
        |--------------------------------------------------------------------------
        */
        let newLength = this.value.length;
        cursorPosition += (newLength - originalLength);

        this.setSelectionRange(
            cursorPosition,
            cursorPosition
        );
    });

});


/*
|--------------------------------------------------------------------------
| FORMAT NUMBER WITH COMMAS
|--------------------------------------------------------------------------
| Examples:
| 5000      => 5,000
| 5000.5    => 5,000.5
| 5000.50   => 5,000.50
|--------------------------------------------------------------------------
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

</script>
