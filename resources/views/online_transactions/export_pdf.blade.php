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
    Online Transactions
</h3>

<table>

    <thead>

        <tr>
            <th>Invoice</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Cash</th>
            <th>Cheque</th>
            <th>POS</th>
            <th>Transfer</th>
            <th>Difference</th>
            <th>Shift</th>
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

                <td>{{ number_format($record->difference, 2) }}</td>

                <td>{{ ucfirst($record->shift) }}</td>

                <td>{{ $record->date->format('d M Y') }}</td>

            </tr>

        @endforeach

    </tbody>

</table>

</body>
</html>