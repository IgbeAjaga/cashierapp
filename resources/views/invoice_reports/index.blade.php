@extends('layouts.app')

@section('title', 'Invoice Reports')
@section('page-title', 'Invoice Reports')

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
                            Invoice Reports
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER CARD -->
        <div class="card mb-2">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-1 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Shift</label>
                            <select name="shift" class="form-select">
                                <option value="">All</option>
                                <option value="morning" @selected(request('shift') == 'morning')>Morning</option>
                                <option value="afternoon" @selected(request('shift') == 'afternoon')>Afternoon</option>
                                <option value="night" @selected(request('shift') == 'night')>Night</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Invoice / Customer">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Rows</label>
                            <select name="per_page" class="form-select">
                                <option value="25">25</option>
                                <option value="50" selected>50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="col-12 mt-1 d-flex flex-wrap gap-1">
                            <button type="submit" class="btn btn-dark btn-sm">
                                <i data-feather="filter"></i> Filter
                            </button>
                            <a href="{{ route('invoice-reports.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            <a href="{{ route('invoice-reports.export', 'excel') }}?{{ http_build_query(request()->query()) }}" class="btn btn-success btn-sm">Export Excel</a>
                            <a href="{{ route('invoice-reports.export', 'pdf') }}?{{ http_build_query(request()->query()) }}" class="btn btn-danger btn-sm">Export PDF</a>
                            <a href="{{ route('invoice-reports.import.form') }}" class="btn btn-outline-primary btn-sm">
                                <i data-feather="upload"></i> Import from Excel / CSV
                            </a>
                            <a href="{{ route('invoice-reports.create') }}" class="btn btn-primary btn-sm">New Entry</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark text-nowrap" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th width="200">Actions</th>
                                <th>#</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Entry Date</th>
                                <th>Total</th>
                                <th>Cash</th>
                                <th>Awaiting</th>
                                <th>Cheque</th>
                                <th>Credit</th>
                                <th>POS</th>
                                <th>Transfer</th>
                                <th>Balance To Use</th>
                                <th>Less Credit</th>
                                <th>Difference</th>
                                <th>Return Outward</th>
                                <th>Invoice To Invoice</th>
                                <th>Return Inward</th>
                                <th>Payment Note</th>
                                <th>Cheque Details</th>
                                <th>POS Details</th>
                                <th>Transfer Details</th>                                
                                <th>Edit Count</th>
                                <th>Recorded By</th>
                                <th>Edited By</th>
                                <th>Last Edited</th>
                                <th>Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoiceReports as $report)
                                <tr class="{{ !$report->is_balanced ? 'table-danger' : '' }}">
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('invoice-reports.edit', $report) }}" class="btn btn-sm btn-dark">Edit</a>
                                            <button class="btn btn-sm btn-warning" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#historyModal{{ $report->id }}">
                                                History
                                            </button>
                                        </div>
                                    </td>
                                    <!-- Serial Number -->
                                    <td>{{ $loop->iteration + ($invoiceReports->currentPage() - 1) * $invoiceReports->perPage() }}</td>
                                    <td><strong>{{ $report->invoice_number }}</strong></td>
                                    <td>{{ $report->customer ?? '-' }}</td>
                                    <td>{{ $report->created_at->format('d M Y h:i A') }}</td>
                                    <td class="fw-bold">{{ number_format($report->total_amount, 2) }}</td>
                                    <td>{{ number_format($report->cash, 2) }}</td>
                                    <td>{{ number_format($report->awaiting_invoice, 2) }}</td>
                                    <td>{{ number_format($report->cheque, 2) }}</td>
                                    <td>{{ number_format($report->credit, 2) }}</td>
                                    <td>{{ number_format($report->pos, 2) }}</td>
                                    <td>{{ number_format($report->bank_transfer, 2) }}</td>
                                    <td>{{ number_format($report->balance_to_use, 2) }}</td>
                                    <td>{{ number_format($report->less_credit ?? 0, 2) }}</td>
                                    <td>
                                        @if($report->difference != 0)
                                            <span class="badge bg-danger">{{ number_format($report->difference, 2) }}</span>
                                        @else
                                            <span class="badge bg-success">Balanced</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($report->return_outward ?? 0, 2) }}</td>
                                    <td>{{ number_format($report->invoice_to_invoice ?? 0, 2) }}</td>
                                    <td>{{ number_format($report->return_inward ?? 0, 2) }}</td>
                                    <td>{{ $report->payment_note ?? '-' }}</td>
                                    <td>{{ $report->cheque_details ?? '-' }}</td>
                                    <td>{{ $report->pos_details ?? '-' }}</td>
                                    <td>{{ $report->bank_transfer_details ?? '-' }}</td>                                    
                                    <td>
                                        @if($report->histories_count > 0)
                                            <span class="badge bg-warning">{{ $report->histories_count }}</span>
                                        @else
                                            <span class="badge bg-light-secondary">0</span>
                                        @endif
                                    </td>
                                    <td>{{ $report->recordedBy?->name ?? '-' }}</td>
                                    <td>{{ $report->editedBy?->name ?? '-' }}</td>
                                    <td>{{ $report->last_edited_at?->diffForHumans() ?? 'Never' }}</td>
                                    <td><span class="badge bg-primary">{{ ucfirst($report->shift ?? '-') }}</span></td>
                                </tr>

                                <!-- HISTORY MODAL -->
                                <div class="modal fade" id="historyModal{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit History - {{ $report->invoice_number }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                @forelse($report->histories as $history)
                                                    <div class="border rounded p-3 mb-3">
                                                        <strong>Edited by {{ optional($history->editor)->name ?? 'System' }}</strong><br>
                                                        <small class="text-muted">{{ $history->created_at->format('d M Y h:i A') }}</small>
                                                        <hr>
                                                        @foreach($history->old_values as $field => $oldValue)
                                                            @php $newValue = $history->new_values[$field] ?? null; @endphp
                                                            @if($oldValue != $newValue)
                                                                <div class="mb-2">
                                                                    <strong>{{ ucwords(str_replace('_', ' ', $field)) }}:</strong>
                                                                    <span class="text-danger">{{ $oldValue ?? '-' }}</span> →
                                                                    <span class="text-success">{{ $newValue ?? '-' }}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @empty
                                                    <p class="text-muted">No edit history found.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="19" class="text-center text-muted">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>

                        <!-- TOTALS -->
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td colspan="5"><strong>TOTAL</strong></td>
                                <td>{{ number_format($totals['total_amount'], 2) }}</td>
                                <td>{{ number_format($totals['cash'], 2) }}</td>
                                <td>{{ number_format($totals['awaiting_invoice'], 2) }}</td>
                                <td>{{ number_format($totals['cheque'], 2) }}</td>
                                <td>{{ number_format($totals['credit'], 2) }}</td>
                                <td>{{ number_format($totals['pos'], 2) }}</td>
                                <td>{{ number_format($totals['bank_transfer'], 2) }}</td>
                                <td>{{ number_format($totals['balance_to_use'], 2) }}</td>
                                <td>{{ number_format($totals['less_credit'], 2) }}</td>
                                <td>{{ number_format($totals['difference'], 2) }}</td>
                                <td>{{ number_format($totals['return_outward'], 2) }}</td>
                                <td>{{ number_format($totals['invoice_to_invoice'], 2) }}</td>
                                <td>{{ number_format($totals['add_return_inward'], 2) }}</td>
                                <td colspan="14"></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="mt-3">
    {{ $invoiceReports->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
                </div>                
            </div>
        </div>
    </div>
</div>
@endsection