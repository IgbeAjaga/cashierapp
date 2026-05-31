@extends('layouts.app')

@section('title', 'Edit Invoice Report')
@section('page-title', 'Edit Invoice Report')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <!-- Header / Breadcrumbs -->
        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Edit Invoice Report</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('invoice-reports.index') }}">Invoice Reports</a>
                                </li>
                                <li class="breadcrumb-item active">Edit Record</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="content-body">
            <div class="card">
                <div class="card-body">

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Error Messages --}}
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('invoice-reports.update', $invoiceReport) }}">
                        @csrf
                        @method('PUT')

                        @include('invoice_reports.form')
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection