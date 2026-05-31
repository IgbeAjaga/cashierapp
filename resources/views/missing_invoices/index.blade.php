@extends('layouts.app')

@section('title', 'Missing Invoices')
@section('page-title', 'Missing Invoices')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

    <!-- BREADCRUMB -->
        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">
                           Missing Invoice
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER CARD -->
        <div class="card mb-2">
            <div class="card-body">
        <form method="GET"
              action="{{ route('missing-invoices.missing') }}"
              class="row g-2 mb-3">

            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date"
                       name="start_date"
                       value="{{ request('start_date') }}"
                       class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date"
                       name="end_date"
                       value="{{ request('end_date') }}"
                       class="form-control">
            </div>

            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-dark w-100">
                    Filter
                </button>
            </div>

            <div class="col-md-2 align-self-end">
                <a href="{{ route('missing-invoices.missing') }}"
                   class="btn btn-outline-secondary w-100">
                    Reset
                </a>
            </div>

        </form>
        </div>
        </div>

        {{-- RESULTS CARD --}}
        <div class="card">
            <div class="card-body">

                <h4 class="mb-3">Missing Invoices</h4>

                @php
                    $missingInvoices = $missing ?? [];
                @endphp

                @if(!empty($missingInvoices) && count($missingInvoices) > 0)

                    <div class="alert alert-warning">
                        <strong>{{ count($missingInvoices) }}</strong> Missing Invoice(s) Found
                    </div>

                    <div class="row">
                        @foreach($missingInvoices as $invoice)
                            <div class="col-md-2 mb-2">
                                <span class="badge bg-danger w-100 p-2">
                                    #{{ $invoice }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                @else

                    <div class="alert alert-success">
                        No missing invoices found for selected period.
                    </div>

                @endif

            </div>
        </div>

    </div>
</div>
@endsection