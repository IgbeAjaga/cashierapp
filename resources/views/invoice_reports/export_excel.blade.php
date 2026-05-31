<table>
    <thead>
        <tr>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Shift</th>
            <th>Total Amount</th>
            <th>Cash</th>
            <th>POS</th>
            <th>Transfer</th>
            <th>Difference</th>
            <th>Recorded By</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->invoice_number }}</td>
            <td>{{ $record->customer }}</td>
            <td>{{ $record->date?->format('d M Y') }}</td>
            <td>{{ ucfirst($record->shift) }}</td>
            <td>{{ number_format($record->total_amount, 2) }}</td>
            <td>{{ number_format($record->cash, 2) }}</td>
            <td>{{ number_format($record->pos, 2) }}</td>
            <td>{{ number_format($record->bank_transfer, 2) }}</td>
            <td>{{ number_format($record->difference, 2) }}</td>
            <td>{{ optional($record->recordedBy)->name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>