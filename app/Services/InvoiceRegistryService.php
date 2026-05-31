<?php

namespace App\Services;

use Exception;
use App\Models\InvoiceRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceRegistryService
{
    private const MAX_ALLOWED_GAP = 500;
    private const DEFAULT_STARTING_NUMBER = 397709;

    /*
    |--------------------------------------------------------------------------
    | CROSS TABLE CHECK (SOURCE OF TRUTH)
    |--------------------------------------------------------------------------
    */
    public static function checkAcrossAllTables(int $invoiceNumber): array
    {
        $models = [
            \App\Models\InvoiceReport::class,
            \App\Models\AwaitingReport::class,
            \App\Models\OnlineTransaction::class,
        ];

        foreach ($models as $model) {

            if ($model::query()->where('invoice_number', $invoiceNumber)->exists()) {

                return [
                    'exists' => true,
                    'table' => (new $model)->getTable(),
                    'message' => "Invoice {$invoiceNumber} already exists in " . (new $model)->getTable(),
                ];
            }
        }

        return ['exists' => false];
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRY CHECK
    |--------------------------------------------------------------------------
    */
    public static function invoiceExists(int $invoiceNumber): ?InvoiceRegistry
    {
        return InvoiceRegistry::query()
            ->where('invoice_number', $invoiceNumber)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | SUSPICIOUS DETECTION
    |--------------------------------------------------------------------------
    */
    public static function detectSuspicious(int $invoiceNumber): array
    {
        $latest = InvoiceRegistry::query()
            ->max('invoice_number');

        if (!$latest) {
            return [
                'is_suspicious' => false,
                'reason' => null,
                'difference' => 0,
            ];
        }

        $difference = $invoiceNumber - $latest;

        if ($difference > self::MAX_ALLOWED_GAP) {

            return [
                'is_suspicious' => true,
                'difference' => $difference,
                'reason' => "Invoice {$invoiceNumber} is far from latest {$latest}. Gap: {$difference}",
            ];
        }

        return [
            'is_suspicious' => false,
            'reason' => null,
            'difference' => $difference,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER INVOICE (CORE METHOD)
    |--------------------------------------------------------------------------
    */
            public static function register(
        int $invoiceNumber,
        string $sourceTable,
        int $sourceId,
        int $userId
    ): InvoiceRegistry {

        return DB::transaction(function () use ($invoiceNumber, $sourceTable, $sourceId, $userId) {

            // Simple registry check
            if (self::invoiceExists($invoiceNumber)) {
                throw new Exception("Invoice {$invoiceNumber} already exists in registry.");
            }

            $suspicious = self::detectSuspicious($invoiceNumber);

            return InvoiceRegistry::create([
                'invoice_number'    => $invoiceNumber,
                'source_table'      => $sourceTable,
                'source_id'         => $sourceId,
                'is_suspicious'     => $suspicious['is_suspicious'],
                'suspicious_reason' => $suspicious['reason'],
                'created_by'        => $userId,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE
    |--------------------------------------------------------------------------
    */
    public static function remove(string $sourceTable, int $sourceId): void
    {
        InvoiceRegistry::query()
            ->where('source_table', $sourceTable)
            ->where('source_id', $sourceId)
            ->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | NEXT INVOICE NUMBER
    |--------------------------------------------------------------------------
    */
    public static function nextInvoiceNumber(): int
    {
        $last = InvoiceRegistry::query()
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        return $last
            ? $last + 1
            : self::DEFAULT_STARTING_NUMBER;
    }

    /*
    |--------------------------------------------------------------------------
    | MISSING INVOICES
    |--------------------------------------------------------------------------
    */
    public static function missingInvoices(int $limit = 200): array
    {
        $numbers = InvoiceRegistry::query()
            ->pluck('invoice_number')
            ->map(fn ($n) => (int) $n)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        if (count($numbers) < 2) {
            return [];
        }

        $missing = [];

        $start = $numbers[0];
        $end = end($numbers);

        $lookup = array_flip($numbers);

        for ($i = $start; $i <= $end; $i++) {

            if (!isset($lookup[$i])) {

                $missing[] = $i;

                if (count($missing) >= $limit) {
                    break;
                }
            }
        }

        return $missing;
    }

    /*
    |--------------------------------------------------------------------------
    | REBUILD REGISTRY
    |--------------------------------------------------------------------------
    */
    public static function rebuildRegistry(): void
    {
        DB::transaction(function () {

            InvoiceRegistry::truncate();

            $models = [
                \App\Models\InvoiceReport::class,
                \App\Models\AwaitingReport::class,
                \App\Models\OnlineTransaction::class,
            ];

            foreach ($models as $model) {

                $model::query()
                    ->select('id', 'invoice_number', 'recorded_by')
                    ->chunk(1000, function ($records) use ($model) {

                        foreach ($records as $record) {

                            try {
                                self::register(
                                    invoiceNumber: (int) $record->invoice_number,
                                    sourceTable: (new $model)->getTable(),
                                    sourceId: $record->id,
                                    userId: $record->recorded_by ?? 1
                                );
                            } catch (Exception $e) {

                                Log::warning('Rebuild duplicate skipped', [
                                    'invoice' => $record->invoice_number,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    });
            }
        });
    }
}