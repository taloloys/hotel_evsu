<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CreditAccount;
use App\Models\CreditAccountLedger;
use Exception;
use Illuminate\Support\Facades\DB;

class CreditBillingService
{
    /**
     * Post a charge from POS or Frontdesk to a credit account.
     *
     * @param  string  $referenceType  e.g., 'pos_order', 'folio'
     *
     * @throws Exception if credit limit is exceeded
     */
    public function chargeAccount(
        CreditAccount $account,
        float $amount,
        string $referenceType,
        ?int $referenceId = null,
        ?int $processedBy = null,
        ?string $notes = null
    ): CreditAccountLedger {
        return DB::transaction(function () use ($account, $amount, $referenceType, $referenceId, $processedBy, $notes) {
            // Lock the account to prevent race conditions during balance check
            $account = CreditAccount::where('account_id', $account->account_id)->lockForUpdate()->firstOrFail();

            if (! $account->is_active) {
                throw new Exception('Cannot charge inactive credit account.');
            }

            if ($account->available_credit < $amount) {
                throw new Exception("Charge amount ({$amount}) exceeds available credit ({$account->available_credit}).");
            }

            $ledger = CreditAccountLedger::create([
                'account_id' => $account->account_id,
                'type' => 'charge',
                'amount' => $amount,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'processed_by' => $processedBy,
                'notes' => $notes,
            ]);

            ActivityLog::log(
                'ACCOUNT_CHARGED',
                'Charged ₱'.number_format($amount, 2)." to Account ({$account->account_name}). Reference: ".($notes ?? "{$referenceType} #{$referenceId}")
            );

            return $ledger;
        });
    }

    /**
     * Record a payment to reduce the outstanding balance of a credit account.
     */
    public function recordPayment(
        CreditAccount $account,
        float $amount,
        ?int $processedBy = null,
        ?string $notes = null
    ): CreditAccountLedger {
        return CreditAccountLedger::create([
            'account_id' => $account->account_id,
            'type' => 'payment',
            'amount' => $amount,
            'reference_type' => 'manual',
            'processed_by' => $processedBy,
            'notes' => $notes,
        ]);
    }
}
