@extends('layouts.app')

@section('title', 'View Invoice Report')
@section('page-title', 'View Invoice Report')

@section('content')
<div class="app-content content">

    <div class="content-wrapper container-xxl p-0">

        <div class="card">

            <div class="card-header d-flex justify-content-between">
                <h4>
                    Invoice Report Details
                </h4>

                <a
                    href="{{ route('invoice-reports.edit', $invoiceReport) }}"
                    class="btn btn-dark"
                >
                    Edit Record
                </a>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered">

                        <tbody>

                            <tr>
                                <th width="250">Invoice Number</th>
                                <td>{{ $invoiceReport->invoice_number }}</td>
                            </tr>

                            <tr>
                                <th>Customer</th>
                                <td>{{ $invoiceReport->customer }}</td>
                            </tr>

                            <tr>
                                <th>Total Amount</th>
                                <td>{{ number_format($invoiceReport->total_amount, 2) }}</td>
                            </tr>

                            <tr>
                                <th>Cash</th>
                                <td>{{ number_format($invoiceReport->cash, 2) }}</td>
                            </tr>

                            <tr>
                                <th>POS</th>
                                <td>{{ number_format($invoiceReport->pos, 2) }}</td>
                            </tr>

                            <tr>
                                <th>Bank Transfer</th>
                                <td>{{ number_format($invoiceReport->bank_transfer, 2) }}</td>
                            </tr>

                            <tr>
                                <th>Difference</th>
                                <td>
                                    {{ number_format($invoiceReport->difference, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <th>Recorded By</th>
                                <td>
                                    {{ $invoiceReport->recordedBy?->name }}
                                </td>
                            </tr>

                            <tr>
                                <th>Edited By</th>
                                <td>
                                    {{ $invoiceReport->editedBy?->name ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Last Edited</th>
                                <td>
                                    {{ $invoiceReport->last_edited_at?->diffForHumans() ?? 'Never Edited' }}
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection