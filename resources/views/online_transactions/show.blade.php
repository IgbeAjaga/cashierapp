@extends('layouts.app')

@section('title', 'View Online Transaction')
@section('page-title', 'View Online Transaction')

@section('content')
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Online Transaction Details</h4>
                <a href="{{ route('online-transactions.edit', $onlineTransaction) }}" class="btn btn-dark">Edit Record</a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th width="250">Invoice Number</th><td>{{ $onlineTransaction->invoice_number }}</td></tr>
                            <tr><th>Customer</th><td>{{ $onlineTransaction->customer }}</td></tr>
                            <tr><th>Total Amount</th><td>{{ number_format($onlineTransaction->total_amount, 2) }}</td></tr>
                            <tr><th>Cash</th><td>{{ number_format($onlineTransaction->cash, 2) }}</td></tr>
                            <tr><th>Cheque</th><td>{{ number_format($onlineTransaction->cheque, 2) }}</td></tr>
                            <tr><th>POS</th><td>{{ number_format($onlineTransaction->pos, 2) }}</td></tr>
                            <tr><th>Transfer</th><td>{{ number_format($onlineTransaction->transfer, 2) }}</td></tr>
                            <tr><th>Return Inward</th><td>{{ number_format($onlineTransaction->return_inward ?? 0, 2) }}</td></tr>
                            <tr><th>Less Credit</th><td>{{ number_format($onlineTransaction->less_credit ?? 0, 2) }}</td></tr>
                            <tr><th>Balance To Use</th><td>{{ number_format($onlineTransaction->balance_to_use, 2) }}</td></tr>
                            <tr><th>Difference</th><td>{{ number_format($onlineTransaction->difference, 2) }}</td></tr>
                            <tr><th>Shift</th><td>{{ ucfirst($onlineTransaction->shift) }}</td></tr>
                            <tr><th>Date</th><td>{{ $onlineTransaction->date?->format('d M Y') }}</td></tr>
                            <tr><th>Cheque Details</th><td>{{ $onlineTransaction->cheque_details ?? '-' }}</td></tr>
                            <tr><th>Recorded By</th><td>{{ $onlineTransaction->recordedBy?->name }}</td></tr>
                            <tr><th>Edited By</th><td>{{ $onlineTransaction->editedBy?->name ?? '-' }}</td></tr>
                            <tr><th>Last Edited</th><td>{{ $onlineTransaction->last_edited_at?->diffForHumans() ?? 'Never Edited' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection