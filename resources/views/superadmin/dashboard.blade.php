@extends('layouts.app')

@section('title', 'Superadmin Dashboard')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">

        {{-- Page Header --}}
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">
                            Superadmin Dashboard
                        </h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Superadmin Dashboard
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Body --}}
        <div class="content-body">
            <section>
                <div class="row">

                    {{-- Pending Users --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Pending Users</h6>
                                        <h2 class="fw-bolder mb-1 text-warning">
                                            {{ \App\Models\User::where('is_approved', false)->count() }}
                                        </h2>
                                        <a href="{{ route('superadmin.users.index') }}" class="text-warning">
                                            Approve Users →
                                        </a>
                                    </div>
                                    <div class="avatar bg-light-warning">
                                        <div class="avatar-content">
                                            <i data-feather="user-x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Users --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Total Users</h6>
                                        <h2 class="fw-bolder mb-1">
                                            {{ \App\Models\User::count() }}
                                        </h2>
                                        <a href="{{ route('superadmin.users.index') }}" class="text-primary">
                                            View All Users →
                                        </a>
                                    </div>
                                    <div class="avatar bg-light-primary">
                                        <div class="avatar-content">
                                            <i data-feather="users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Invoice Reports --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Total Invoices</h6>
                                        <h2 class="fw-bolder mb-1 text-success">
                                            {{ \App\Models\InvoiceReport::count() }}
                                        </h2>
                                        <a href="{{ route('invoice-reports.index') }}" class="text-success">
                                            View Reports →
                                        </a>
                                    </div>
                                    <div class="avatar bg-light-success">
                                        <div class="avatar-content">
                                            <i data-feather="file-text"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Today's Sales --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Today's Sales</h6>
                                        <h2 class="fw-bolder mb-1 text-info">
                                            ₦{{ number_format(\App\Models\InvoiceReport::whereDate('date', today())->sum('total_amount'), 2) }}
                                        </h2>
                                        <span class="text-info">Invoice Reports</span>
                                    </div>
                                    <div class="avatar bg-light-info">
                                        <div class="avatar-content">
                                            <i data-feather="dollar-sign"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Quick Access Section -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Quick Access</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('invoice-reports.index') }}" class="btn btn-outline-primary w-100 py-2">
                                            <i data-feather="file-text"></i> Invoice Reports
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('awaiting-reports.index') }}" class="btn btn-outline-warning w-100 py-2">
                                            <i data-feather="clock"></i> Awaiting Reports
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('online-transactions.index') }}" class="btn btn-outline-success w-100 py-2">
                                            <i data-feather="globe"></i> Online Transactions
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-info w-100 py-2">
                                            <i data-feather="shield"></i> User Management
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </div>

    </div>
</div>
@endsection