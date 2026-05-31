<?php

namespace App\Http\Controllers;

use App\Services\InvoiceRegistryService;

class MissingInvoiceController extends Controller
{
    public function index()
    {
        $missing = InvoiceRegistryService::missingInvoices(200);

        return view('missing_invoices.index', compact('missing'));
    }
}