<?php

namespace App\Services;

class BalanceService
{
    /**
     * Calculate customer payment balance
     *
     * return_inward  = Credit given to customer for returned goods
     *                (reduces what customer owes you)
     */
    public static function calculate(array $data): array
    {
        $totalAmount     = (float) ($data['total_amount'] ?? 0);
        $returnInward    = (float) ($data['return_inward'] ?? 0);   // Credit to customer
        $returnOutward    = (float) ($data['return_outward'] ?? 0);
        $cash            = (float) ($data['cash'] ?? 0);
        $awaiting        = (float) ($data['awaiting_invoice'] ?? 0);
        $cheque          = (float) ($data['cheque'] ?? 0);
        $credit          = (float) ($data['credit'] ?? 0);
        $pos             = (float) ($data['pos'] ?? 0);
        $bankTransfer    = (float) ($data['bank_transfer'] ?? 0);
        $lessCredit      = (float) ($data['less_credit'] ?? 0);
         $invoicetoInvoice     = (float) ($data['invoice_to_invoice'] ?? 0);

        // Net amount customer actually owes after returns
        $effectiveAmount = $totalAmount - $returnInward;

        // Total money received from customer
        $totalPaid = 
            $cash +            
            $cheque +            
            $pos +
            $bankTransfer +
            $returnOutward +
            $invoicetoInvoice +            
            $lessCredit;

        // Difference (positive = customer still owes, negative = overpaid / credit)
        $difference = round($effectiveAmount - $totalPaid, 2);

        // Balance to use (credit customer can use for next purchase)
        $balanceToUse = 0;
        if ($totalPaid > $effectiveAmount) {
            $balanceToUse = round($totalPaid - $effectiveAmount, 2);
        }

        return [
            'difference'       => $difference,           // What customer still owes you
            'balance_to_use'   => $balanceToUse,         // Credit for customer
            'is_balanced'      => abs($difference) < 0.01,
            'effective_amount' => $effectiveAmount,
            'return_inward'    => $returnInward,
        ];
    }
}