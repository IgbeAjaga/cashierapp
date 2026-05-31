@extends('layouts.app')

@section('title', 'Add Awaiting Report')
@section('page-title', 'Add Awaiting Report')

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
                        <h2 class="content-header-title float-start mb-0">Add Awaiting Report</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('awaiting-reports.index') }}">Awaiting Reports</a>
                                </li>
                                <li class="breadcrumb-item active">Add Record</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
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

                    {{-- Duplicate / General Error --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong>

        <div class="mt-50">
            {!! session('error') !!}
        </div>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>
    </div>
@endif

{{-- Validation Errors --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <strong>Please fix the following:</strong>

        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>
    </div>
@endif

                    {{-- Warning Message (Suspicious Invoice) --}}
                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show">
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('awaiting-reports.store') }}">
                        @csrf

                        @include('awaiting_reports.form')
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection