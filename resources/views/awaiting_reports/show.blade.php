@extends('layouts.app')

@section('title', 'View Awaiting Report')
@section('page-title', 'View Awaiting Report')

@section('content')
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Awaiting Report Details</h4>
                <a href="{{ route('awaiting-reports.edit', $awaitingReport) }}" class="btn btn-dark">Edit Record</a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th width="250">Invoice Number</th><td>{{ $awaitingReport->invoice_number }}</td></tr>
                            <tr><th>Customer</th><td>{{ $awaitingReport->customer }}</td></tr>
                            <tr><th>Total Amount</th><td>{{ number_format($awaitingReport->total_amount, 2) }}</td></tr>
                            <tr><th>Cash</th><td>{{ number_format($awaitingReport->cash, 2) }}</td></tr>
                            <tr><th>Cheque</th><td>{{ number_format($awaitingReport->cheque, 2) }}</td></tr>
                            <tr><th>POS</th><td>{{ number_format($awaitingReport->pos, 2) }}</td></tr>
                            <tr><th>Bank Transfer</th><td>{{ number_format($awaitingReport->bank_transfer, 2) }}</td></tr>
                            <tr><th>Return Inward</th><td>{{ number_format($awaitingReport->return_inward ?? 0, 2) }}</td></tr>
                            <tr><th>Balance To Use</th><td>{{ number_format($awaitingReport->balance_to_use, 2) }}</td></tr>
                            <tr><th>Difference</th><td>{{ number_format($awaitingReport->difference, 2) }}</td></tr>
                            <tr><th>Shift</th><td>{{ ucfirst($awaitingReport->shift) }}</td></tr>
                            <tr><th>Date</th><td>{{ $awaitingReport->date?->format('d M Y') }}</td></tr>
                            <tr><th>Recorded By</th><td>{{ $awaitingReport->recordedBy?->name }}</td></tr>
                            <tr><th>Edited By</th><td>{{ $awaitingReport->editedBy?->name ?? '-' }}</td></tr>
                            <tr><th>Last Edited</th><td>{{ $awaitingReport->last_edited_at?->diffForHumans() ?? 'Never Edited' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection