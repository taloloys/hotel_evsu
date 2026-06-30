<?php

use App\Models\CreditAccount;
use App\Models\CreditAccountLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it calculates derived balances correctly from the ledger', function () {
    // Arrange
    $account = CreditAccount::create([
        'account_name' => 'Acme Corp',
        'credit_limit' => 10000,
        'is_active' => true,
    ]);

    // Initial state
    expect($account->outstanding_balance)->toBe(0.0)
        ->and($account->available_credit)->toBe(10000.0);

    // Act: Charge 1
    CreditAccountLedger::create([
        'account_id' => $account->account_id,
        'type' => 'charge',
        'amount' => 2000,
        'reference_type' => 'pos_order',
    ]);

    // Act: Charge 2
    CreditAccountLedger::create([
        'account_id' => $account->account_id,
        'type' => 'charge',
        'amount' => 3500,
        'reference_type' => 'folio',
    ]);

    // Verify after charges
    expect($account->fresh()->outstanding_balance)->toBe(5500.0)
        ->and($account->fresh()->available_credit)->toBe(4500.0);

    // Act: Payment
    CreditAccountLedger::create([
        'account_id' => $account->account_id,
        'type' => 'payment',
        'amount' => 5000,
        'reference_type' => 'manual',
    ]);

    // Verify after payment
    expect($account->fresh()->outstanding_balance)->toBe(500.0)
        ->and($account->fresh()->available_credit)->toBe(9500.0);
});
