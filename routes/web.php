<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    DashboardController,
    InvoiceReportController,
    AwaitingReportController,
    OnlineTransactionController,
    UserApprovalController,
    MissingInvoiceController,
    InvoiceSearchController
};

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USERS
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| SUPERADMIN ONLY
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'superadmin'
])->prefix('superadmin')->name('superadmin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | SUPERADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'superAdmin']
    )->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/users',
        [UserApprovalController::class, 'index']
    )->name('users.index');

    Route::patch(
        '/users/{user}/approve',
        [UserApprovalController::class, 'approve']
    )->name('users.approve');

    Route::patch(
        '/users/{user}/disapprove',
        [UserApprovalController::class, 'disapprove']
    )->name('users.disapprove');

});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'admin'
])->prefix('admin')->name('admin.')->group(function () {

    Route::get(
        '/dashboard',
        [DashboardController::class, 'admin']
    )->name('dashboard');

});

/*
|--------------------------------------------------------------------------
| APPROVED USERS
|--------------------------------------------------------------------------
|
| Shared modules:
| - Superadmin
| - Admin
| - Cashier
| - Supervisor
| - Accountant
|
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'approved'
])->group(function () {

Route::get(
    '/missing-invoices',
    [MissingInvoiceController::class, 'index']
)->name('missing-invoices.missing');

Route::get(
    '/invoice-search',
    [InvoiceSearchController::class, 'index']
)->name('invoice-search.index');

    /*
    |--------------------------------------------------------------------------
    | INVOICE REPORTS
    |--------------------------------------------------------------------------
    */

    Route::prefix('invoice-reports')
        ->name('invoice-reports.')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | EXPORTS
            |--------------------------------------------------------------------------
            */

            Route::get(
                'export/{format}',
                [InvoiceReportController::class, 'export']
            )->name('export');

            Route::get(
                'export/pdf',
                [InvoiceReportController::class, 'exportPdf']
            )->name('export.pdf');

           /*
            |--------------------------------------------------------------------------
            | IMPORTS
            |--------------------------------------------------------------------------
            */
        Route::get('import', [InvoiceReportController::class, 'importForm'])->name('import.form');
        Route::post('import', [InvoiceReportController::class, 'import'])->name('import');
        Route::get('import/template', [InvoiceReportController::class, 'importTemplate'])->name('import.template');

            /*
            |--------------------------------------------------------------------------
            | CUSTOM ROUTES
            |--------------------------------------------------------------------------
            */

            Route::get(
                'balanced',
                [InvoiceReportController::class, 'balanced']
            )->name('balanced');

            Route::get(
                'unbalanced',
                [InvoiceReportController::class, 'unbalanced']
            )->name('unbalanced');

            /*
            |--------------------------------------------------------------------------
            | RESOURCE
            |--------------------------------------------------------------------------
            */

            Route::resource('/', InvoiceReportController::class)
                ->parameters([
                    '' => 'invoiceReport'
                ])
                ->names([
                    'index' => 'index',
                    'create' => 'create',
                    'store' => 'store',
                    'show' => 'show',
                    'edit' => 'edit',
                    'update' => 'update',
                    'destroy' => 'destroy',
                ]);
        });

    /*
    |--------------------------------------------------------------------------
    | AWAITING REPORTS
    |--------------------------------------------------------------------------
    */

    Route::prefix('awaiting-reports')
        ->name('awaiting-reports.')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | EXPORTS
            |--------------------------------------------------------------------------
            */

            Route::get(
                'export/{format}',
                [AwaitingReportController::class, 'export']
            )->name('export');

            Route::get(
                'export/pdf',
                [AwaitingReportController::class, 'exportPdf']
            )->name('export.pdf');

            /*
            |--------------------------------------------------------------------------
            | IMPORTS
            |--------------------------------------------------------------------------
            */

        Route::get('import', [AwaitingReportController::class, 'importForm'])->name('import.form');
        Route::post('import', [AwaitingReportController::class, 'import'])->name('import');
        Route::get('import/template', [AwaitingReportController::class, 'importTemplate'])->name('import.template');

            /*
            |--------------------------------------------------------------------------
            | CUSTOM ROUTES
            |--------------------------------------------------------------------------
            */

            Route::get(
                'balanced',
                [AwaitingReportController::class, 'balanced']
            )->name('balanced');

            Route::get(
                'unbalanced',
                [AwaitingReportController::class, 'unbalanced']
            )->name('unbalanced');

            /*
            |--------------------------------------------------------------------------
            | RESOURCE
            |--------------------------------------------------------------------------
            */

            Route::resource('/', AwaitingReportController::class)
                ->parameters([
                    '' => 'awaitingReport'
                ])
                ->names([
                    'index' => 'index',
                    'create' => 'create',
                    'store' => 'store',
                    'show' => 'show',
                    'edit' => 'edit',
                    'update' => 'update',
                    'destroy' => 'destroy',
                ]);
        });

    /*
    |--------------------------------------------------------------------------
    | ONLINE TRANSACTIONS
    |--------------------------------------------------------------------------
    */

    Route::prefix('online-transactions')
        ->name('online-transactions.')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | EXPORTS
            |--------------------------------------------------------------------------
            */

            Route::get(
                'export/{format}',
                [OnlineTransactionController::class, 'export']
            )->name('export');

            Route::get(
                'export/pdf',
                [OnlineTransactionController::class, 'exportPdf']
            )->name('export.pdf');

            /*
            |--------------------------------------------------------------------------
            | IMPORTS
            |--------------------------------------------------------------------------
            */

        Route::get('import', [OnlineTransactionController::class, 'importForm'])->name('import.form');
        Route::post('import', [OnlineTransactionController::class, 'import'])->name('import');
        Route::get('import/template', [OnlineTransactionController::class, 'importTemplate'])->name('import.template');
   

           

            /*
            |--------------------------------------------------------------------------
            | CUSTOM ROUTES
            |--------------------------------------------------------------------------
            */

            Route::get(
                'balanced',
                [OnlineTransactionController::class, 'balanced']
            )->name('balanced');

            Route::get(
                'unbalanced',
                [OnlineTransactionController::class, 'unbalanced']
            )->name('unbalanced');

            /*
            |--------------------------------------------------------------------------
            | RESOURCE
            |--------------------------------------------------------------------------
            */

            Route::resource('/', OnlineTransactionController::class)
                ->parameters([
                    '' => 'onlineTransaction'
                ])
                ->names([
                    'index' => 'index',
                    'create' => 'create',
                    'store' => 'store',
                    'show' => 'show',
                    'edit' => 'edit',
                    'update' => 'update',
                    'destroy' => 'destroy',
                ]);
        });
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';