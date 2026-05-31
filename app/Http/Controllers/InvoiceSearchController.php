<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceRegistry;

class InvoiceSearchController extends Controller
{
    public function index(Request $request)
    {
        $invoice = null;

        if ($request->filled('invoice_number')) {
            $invoice = InvoiceRegistry::with('creator')
                ->where('invoice_number', trim($request->invoice_number))
                ->first();
        }

        return view('invoice-search.index', compact('invoice'));
    }

    /**
     * AJAX endpoint — called from create pages to check before submitting.
     * GET /invoice-search/check?invoice_number=123456
     */
    public function check(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|integer|min:1',
        ]);

        $invoice = InvoiceRegistry::where(
            'invoice_number',
            (int) $request->invoice_number
        )->first();

        if (!$invoice) {
            return response()->json(['found' => false]);
        }

        $moduleNames = [
            'invoice_reports'     => 'Invoice Reports',
            'awaiting_reports'    => 'Awaiting Reports',
            'online_transactions' => 'Online Transactions',
        ];

        $moduleName = $moduleNames[$invoice->source_table]
            ?? ucwords(str_replace('_', ' ', $invoice->source_table));

        return response()->json([
            'found'   => true,
            'table'   => $moduleName,
            'message' => "Invoice {$invoice->invoice_number} already exists in {$moduleName}.",
        ]);
    }
}