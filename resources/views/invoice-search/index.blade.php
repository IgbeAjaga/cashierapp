@extends('layouts.app')

@section('title', 'Search Existing Invoice')
@section('page-title', 'Search Existing Invoice')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        {{-- HEADER --}}
        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <h2 class="content-header-title mb-0">Search Existing Invoice</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Invoice Search</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- SEARCH FORM --}}
        <div class="card mb-2">
            <div class="card-body">
                <form method="GET"
                      action="{{ route('invoice-search.index') }}"
                      class="row g-1 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" for="invoice_number">
                            Invoice Number
                        </label>
                        <input type="text"
                               id="invoice_number"
                               name="invoice_number"
                               class="form-control"
                               value="{{ request('invoice_number') }}"
                               placeholder="Enter invoice number"
                               autofocus
                               required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i data-feather="search" class="me-50"></i>
                            Search
                        </button>
                    </div>
                    @if(request()->filled('invoice_number'))
                        <div class="col-md-2">
                            <a href="{{ route('invoice-search.index') }}"
                               class="btn btn-outline-secondary w-100">
                                Clear
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- RESULTS --}}
        @if(request()->filled('invoice_number'))

            @if($invoice)

                {{-- FOUND --}}
                @php
                    // Compute human-readable module name from source_table
                    $moduleNames = [
                        'invoice_reports'     => 'Invoice Reports',
                        'awaiting_reports'    => 'Awaiting Reports',
                        'online_transactions' => 'Online Transactions',
                    ];
                    $moduleName = $moduleNames[$invoice->source_table] ?? ucwords(str_replace('_', ' ', $invoice->source_table));

                    // Compute edit URL from source_table and source_id
                    $editRoutes = [
                        'invoice_reports'     => 'invoice-reports.edit',
                        'awaiting_reports'    => 'awaiting-reports.edit',
                        'online_transactions' => 'online-transactions.edit',
                    ];
                    $editRoute  = $editRoutes[$invoice->source_table] ?? null;
                    $editUrl    = $editRoute ? route($editRoute, $invoice->source_id) : null;

                    $viewRoutes = [
                        'invoice_reports'     => 'invoice-reports.show',
                        'awaiting_reports'    => 'awaiting-reports.show',
                        'online_transactions' => 'online-transactions.show',
                    ];
                    $viewRoute  = $viewRoutes[$invoice->source_table] ?? null;
                    $viewUrl    = $viewRoute ? route($viewRoute, $invoice->source_id) : null;
                @endphp

                <div class="card">
                    <div class="card-body">

                        <div class="alert {{ $invoice->is_suspicious ? 'alert-danger' : 'alert-success' }} mb-3">
                            <h5 class="mb-0">
                                <i data-feather="{{ $invoice->is_suspicious ? 'alert-triangle' : 'check-circle' }}"
                                   class="me-1"></i>
                                Invoice {{ $invoice->invoice_number }}
                                {{ $invoice->is_suspicious ? '— Suspicious' : 'Found Successfully' }}
                            </h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Invoice Number</th>
                                        <td>
                                            <strong>{{ $invoice->invoice_number }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Module</th>
                                        <td>{{ $moduleName }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created By</th>
                                        <td>{{ optional($invoice->creator)->name ?? 'System' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date Recorded</th>
                                        <td>
                                            {{ $invoice->created_at->format('d M Y h:i A') }}
                                            <small class="text-muted d-block">
                                                {{ $invoice->created_at->diffForHumans() }}
                                            </small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($invoice->is_suspicious)
                                                <span class="badge bg-danger">Suspicious</span>
                                                @if($invoice->suspicious_reason)
                                                    <small class="text-muted d-block mt-1">
                                                        {{ $invoice->suspicious_reason }}
                                                    </small>
                                                @endif
                                            @else
                                                <span class="badge bg-success">Valid</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex flex-wrap gap-1">
                            @if($viewUrl)
                                <a href="{{ $viewUrl }}" class="btn btn-info">
                                    <i data-feather="eye" class="me-50"></i>
                                    View Record
                                </a>
                            @endif
                            @if($editUrl)
                                <a href="{{ $editUrl }}" class="btn btn-primary">
                                    <i data-feather="edit" class="me-50"></i>
                                    Edit Record
                                </a>
                            @endif
                            <a href="{{ route('invoice-search.index') }}"
                               class="btn btn-outline-secondary">
                                New Search
                            </a>
                        </div>

                    </div>
                </div>

            @else

                {{-- NOT FOUND --}}
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-warning mb-0">
                            <h5 class="mb-1">
                                <i data-feather="search" class="me-1"></i>
                                Invoice {{ request('invoice_number') }} Not Found
                            </h5>
                            <p class="mb-0">
                                This invoice number does not exist in any module
                                (Invoice Reports, Awaiting Reports, or Online Transactions).
                            </p>
                        </div>
                    </div>
                </div>

            @endif

        @endif

    </div>
</div>
@endsection