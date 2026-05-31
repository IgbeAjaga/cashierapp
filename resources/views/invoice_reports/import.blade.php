@extends('layouts.app')

@section('title', 'Import Invoice Reports')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <h2 class="content-header-title mb-0">Import Invoice Reports</h2>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('invoice-reports.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Choose Excel/CSV File</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="upload"></i> Upload & Import
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <h5>Download Template</h5>
                <a href="{{ route('invoice-reports.import.template') }}" class="btn btn-outline-secondary">
                    <i data-feather="download"></i> Download Invoice Report Import Template (Excel)
                </a>

                <!-- Sample Table with Sticky Header -->
                <h5 class="mt-4">Expected Columns (Sample)</h5>
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th>invoice_number</th>
                                <th>customer</th>
                                <th>payment_note</th>
                                <th>total_amount</th>
                                <th>cash</th>
                                <th>awaiting_invoice</th>
                                <th>cheque</th>
                                <th>credit</th>
                                <th>pos</th>
                                <th>bank_transfer</th>
                                <th>balance_to_use</th>
                                <th>date</th>
                                <th>shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>394801</td>
                                <td>Medicare Pharmacy</td>
                                <td>Walk-in sale</td>
                                <td>125000</td>
                                <td>80000</td>
                                <td>15000</td>
                                <td>10000</td>
                                <td>0</td>
                                <td>20000</td>
                                <td>0</td>
                                <td>0</td>
                                <td>2026-05-29</td>
                                <td>morning</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <small class="text-muted">Tip: Scroll down — the header stays fixed.</small>
            </div>
        </div>
    </div>
</div>
@endsection