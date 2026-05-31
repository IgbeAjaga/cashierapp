@extends('layouts.app')

@section('title', 'Import Online Transactions')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <h2 class="content-header-title mb-0">Import Online Transactions</h2>
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

                <form action="{{ route('online-transactions.import') }}" method="POST" enctype="multipart/form-data">
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
                <a href="{{ route('online-transactions.import.template') }}" class="btn btn-outline-secondary">
                    <i data-feather="download"></i> Download Online Transaction Import Template (Excel)
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
                                <th>cheque</th>
                                <th>pos</th>
                                <th>transfer</th>
                                <th>return_inward</th>
                                <th>less_credit</th>
                                <th>balance_to_use</th>
                                <th>date</th>
                                <th>shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>394802</td>
                                <td>QuickMart Online</td>
                                <td>Online order payment</td>
                                <td>85000</td>
                                <td>40000</td>
                                <td>0</td>
                                <td>15000</td>
                                <td>30000</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>2026-05-29</td>
                                <td>afternoon</td>
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