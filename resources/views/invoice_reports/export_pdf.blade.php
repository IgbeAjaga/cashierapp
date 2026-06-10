<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
        }

        th {
            background: #f3f3f3;
        }

    </style>

</head>

<body>

<h3>
    Invoice Reports
</h3>

<table>

    <thead>

        <tr>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Shift</th>
            <th>Total Amount</th>
            <th>Cash</th>
            <th>Awaiting</th>
            <th>Cheque</th>
            <th>Credit</th>
            <th>POS</th>
            <th>Transfer</th>
            <th>Less Credit</th>
            <th>Return Inward</th>
            <th>Balance To Use</th>
            <th>Difference</th>
            <th>Return Outward</th>
            <th>Invoice to Invoice</th>
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
                <td>{{ number_format($record->awaiting_invoice, 2) }}</td>
                 <td>{{ number_format($record->cheque, 2) }}</td>
                <td>{{ number_format($record->credit, 2) }}</td>
                <td>{{ number_format($record->pos, 2) }}</td>
                <td>{{ number_format($record->bank_transfer, 2) }}</td>
                <td>{{ number_format($record->less_credit, 2) }}</td>
                <td>{{ number_format($record->return_inward, 2) }}</td>
                <td>{{ number_format($record->balance_to_use, 2) }}</td>
                <td>{{ number_format($record->difference, 2) }}</td>
                <td>{{ number_format($record->return_outward, 2) }}</td>
                <td>{{ number_format($record->invoice_to_invoice, 2) }}</td>

            </tr>

        @endforeach

    </tbody>

</table>

</body>
</html>