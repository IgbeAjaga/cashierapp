<?php

namespace App\Services;

class BalanceService
{
    public static function calculate(array $data): array
    {
        $cash = (float) ($data['cash'] ?? 0);
        $awaiting = (float) ($data['awaiting_invoice'] ?? 0);
        $cheque = (float) ($data['cheque'] ?? 0);
        $credit = (float) ($data['credit'] ?? 0);
        $pos = (float) ($data['pos'] ?? 0);
        $bankTransfer = (float) ($data['bank_transfer'] ?? 0);
        $transfer = (float) ($data['transfer'] ?? 0);
        $lessCredit = (float) ($data['less_credit'] ?? 0);

        $totalAmount = (float) ($data['total_amount'] ?? 0);

        $totalPaid =
            $cash +
            $awaiting +
            $cheque +
            $credit +
            $pos +
            $bankTransfer +
            $transfer +
            $lessCredit;

        $difference = round(
            $totalAmount - $totalPaid,
            2
        );

        $balanceToUse = 0;

        if ($totalPaid > $totalAmount) {
            $balanceToUse = round(
                $totalPaid - $totalAmount,
                2
            );
        }

        return [
            'difference' => $difference,
            'balance_to_use' => $balanceToUse,
            'is_balanced' => abs($difference) < 0.01,
        ];
    }
}