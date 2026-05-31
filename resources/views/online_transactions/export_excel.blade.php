<table>
    <thead>
        <tr>
            <th>Invoice</th>
            <th>Customer</th>
            <th>Total Amount</th>
            <th>Cash</th>
            <th>Cheque</th>
            <th>POS</th>
            <th>Transfer</th>
            <th>Return Inward</th>
            <th>Less Credit</th>
            <th>Balance To Use</th>
            <th>Difference</th>
            <th>Shift</th>
            <th>Recorded By</th>
            <th>Edited By</th>
            <th>Last Edited</th>
            <th>Date</th>
        </tr>
    </thead>

    <tbody>

        @foreach($records as $record)

            <tr>

                <td>{{ $record->invoice_number }}</td>

                <td>{{ $record->customer }}</td>

                <td>{{ number_format($record->total_amount, 2) }}</td>

                <td>{{ number_format($record->cash, 2) }}</td>

                <td>{{ number_format($record->cheque, 2) }}</td>

                <td>{{ number_format($record->pos, 2) }}</td>

                <td>{{ number_format($record->transfer, 2) }}</td>

                <td>{{ number_format($record->return_inward, 2) }}</td>

                <td>{{ number_format($record->less_credit, 2) }}</td>

                <td>{{ number_format($record->balance_to_use, 2) }}</td>

                <td>{{ number_format($record->difference, 2) }}</td>

                <td>{{ ucfirst($record->shift) }}</td>

                <td>{{ optional($record->recordedBy)->name }}</td>

                <td>{{ optional($record->editedBy)->name }}</td>

                <td>
                    @if($record->last_edited_at)
                        {{ $record->last_edited_at->format('d M Y h:i A') }}
                    @else
                        Never Edited
                    @endif
                </td>

                <td>{{ $record->date->format('Y-m-d') }}</td>

            </tr>

        @endforeach

    </tbody>
</table>