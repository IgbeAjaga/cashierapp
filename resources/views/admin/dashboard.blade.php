@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <!-- Dashboard Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title mb-0">Overview</h4>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <!-- Invoice Report Records -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1 text-uppercase">
                                        Invoice Report
                                    </h6>
                                    <h2 class="fw-bold mb-0">
                                        {{ \App\Models\InvoiceReport::count() }}
                                    </h2>
                                </div>

                                <div class="avatar bg-light-primary p-2">
                                    <i data-feather="check-square" class="font-medium-3 text-primary"></i>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-0 pt-0">
                                <a href="{{ route('invoice-reports.index') }}"
                                   class="text-primary fw-semibold">
                                    View Records →
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- You can add more cards here later --}}
                    {{-- Example: Today Records, Pending, Approved, etc --}}

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
