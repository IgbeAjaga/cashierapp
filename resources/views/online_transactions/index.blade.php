@extends('layouts.app')

@section('title', 'Online Transactions')
@section('page-title', 'Online Transactions')

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
                            Online Transactions
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
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Invoice / Customer / Note">
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
                            <a href="{{ route('online-transactions.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            <a href="{{ route('online-transactions.export', 'excel') }}?{{ http_build_query(request()->query()) }}" class="btn btn-success btn-sm">Export Excel</a>
                            <a href="{{ route('online-transactions.export', 'pdf') }}?{{ http_build_query(request()->query()) }}" class="btn btn-danger btn-sm">Export PDF</a>
                            <a href="{{ route('online-transactions.import.form') }}" class="btn btn-outline-primary btn-sm">
                                <i data-feather="upload"></i> Import from Excel / CSV
                            </a>
                            <a href="{{ route('online-transactions.create') }}" class="btn btn-primary btn-sm">New Entry</a>
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
                                <th>Cheque</th>
                                <th>POS</th>
                                <th>Transfer</th>
                                <th>Return Inward</th>
                                <th>Less Credit</th>
                                <th>Balance To Use</th>
                                <th>Difference</th>
                                <th>Payment Note</th>
                                <th>Cheque Details</th>
                                <th>Edit Count</th>
                                <th>Recorded By</th>
                                <th>Edited By</th>
                                <th>Last Edited</th>
                                <th>Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($onlineTransactions as $transaction)
                                <tr class="{{ !$transaction->is_balanced ? 'table-danger' : '' }}">
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('online-transactions.edit', $transaction) }}" class="btn btn-sm btn-dark">Edit</a>
                                            <button class="btn btn-sm btn-warning" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#historyModal{{ $transaction->id }}">
                                                History
                                            </button>
                                        </div>
                                    </td>
                                    <!-- Serial Number -->
                                    <td>{{ $loop->iteration + ($onlineTransactions->currentPage() - 1) * $onlineTransactions->perPage() }}</td>
                                    <td><strong>{{ $transaction->invoice_number }}</strong></td>
                                    <td>{{ $transaction->customer ?? '-' }}</td>
                                    <td>{{ $transaction->created_at->format('d M Y h:i A') }}</td>
                                    <td class="fw-bold">{{ number_format($transaction->total_amount, 2) }}</td>
                                    <td>{{ number_format($transaction->cash, 2) }}</td>
                                    <td>{{ number_format($transaction->cheque, 2) }}</td>
                                    <td>{{ number_format($transaction->pos, 2) }}</td>
                                    <td>{{ number_format($transaction->transfer, 2) }}</td>
                                    <td>{{ number_format($transaction->return_inward ?? 0, 2) }}</td>
                                    <td>{{ number_format($transaction->less_credit ?? 0, 2) }}</td>
                                    <td>{{ number_format($transaction->balance_to_use, 2) }}</td>
                                    <td>
    @if($transaction->difference == 0)

        <span class="badge bg-success">
            Balanced
        </span>

    @elseif($transaction->difference < 0)

        <span class="badge bg-danger">
            Company Owes: {{ number_format(abs($transaction->difference), 2) }}
        </span>

    @else

        <span class="badge bg-warning text-dark">
            Customer Owes: {{ number_format($transaction->difference, 2) }}
        </span>

    @endif
</td>
                                    <td>{{ $transaction->payment_note ?? '-' }}</td>
                                    <td>{{ $transaction->cheque_details ?? '-' }}</td>                                    
                                    <td>
                                        @if($transaction->histories_count > 0)
                                            <span class="badge bg-warning">{{ $transaction->histories_count }}</span>
                                        @else
                                            <span class="badge bg-light-secondary">0</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->recordedBy?->name ?? '-' }}</td>
                                    <td>{{ $transaction->editedBy?->name ?? '-' }}</td>
                                    <td>{{ $transaction->last_edited_at?->diffForHumans() ?? 'Never' }}</td>
                                    <td><span class="badge bg-primary">{{ ucfirst($transaction->shift ?? '-') }}</span></td>
                                </tr>

                                <!-- HISTORY MODAL -->
                                <div class="modal fade" id="historyModal{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit History - {{ $transaction->invoice_number }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                @forelse($transaction->histories as $history)
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
                                <td>{{ number_format($totals['cheque'], 2) }}</td>
                                <td>{{ number_format($totals['pos'], 2) }}</td>
                                <td>{{ number_format($totals['bank_transfer'], 2) }}</td>
                                <td>{{ number_format($totals['return_inward'] ?? 0, 2) }}</td>
                                <td>{{ number_format($totals['less_credit'] ?? 0, 2) }}</td>
                                <td>{{ number_format($totals['balance_to_use'], 2) }}</td>
                                <td>{{ number_format($totals['difference'], 2) }}</td>
                                <td colspan="8"></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="mt-3">
    {{ $onlineTransactions->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
                </div>                
            </div>
        </div>
    </div>
</div>
@endsection