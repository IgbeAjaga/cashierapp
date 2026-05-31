@extends('layouts.app')

@section('title', 'Edit Awaiting Report')
@section('page-title', 'Edit Awaiting Report')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <h2 class="content-header-title mb-0">Edit Awaiting Report</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('awaiting-reports.index') }}">Awaiting Reports</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Awaiting Report</h4>
            </div>

            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

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

                <form action="{{ route('awaiting-reports.update', $awaitingReport) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('awaiting_reports.form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection