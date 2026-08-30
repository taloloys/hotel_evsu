<?php

use App\Models\ChargeCode;
use App\Services\ChargeCodeResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('resolves an active charge code by slug', function () {
    ChargeCode::firstOrCreate(
        ['charge_code' => 999],
        [
            'slug' => 'test_slug',
            'description' => 'TEST CODE',
            'category' => 'HOTEL',
            'is_active' => true,
        ]
    );

    $result = ChargeCodeResolver::resolve('test_slug');

    expect($result)->toBe(999);
});

it('returns null for a non-existent slug', function () {
    $result = ChargeCodeResolver::resolve('nonexistent_slug_xyz');

    expect($result)->toBeNull();
});

it('returns null for an inactive charge code', function () {
    ChargeCode::firstOrCreate(
        ['charge_code' => 998],
        [
            'slug' => 'inactive_test',
            'description' => 'INACTIVE TEST',
            'category' => 'HOTEL',
            'is_active' => false,
        ]
    );

    $result = ChargeCodeResolver::resolve('inactive_test');

    expect($result)->toBeNull();
});

it('resolves payment method to charge code', function () {
    ChargeCode::firstOrCreate(
        ['charge_code' => 997],
        [
            'slug' => 'gcash',
            'description' => 'GCASH',
            'category' => 'PAYMENT',
            'is_active' => true,
        ]
    );

    $result = ChargeCodeResolver::resolvePaymentMethod('GCash');

    expect($result)->toBe(997);
});

it('returns null for unknown payment method', function () {
    $result = ChargeCodeResolver::resolvePaymentMethod('Bitcoin');

    expect($result)->toBeNull();
});

it('maps POS payment labels to correct slugs', function () {
    expect(ChargeCodeResolver::slugForPosPaymentLabel('GCASH'))->toBe('gcash');
    expect(ChargeCodeResolver::slugForPosPaymentLabel('MAYA'))->toBe('maya');
    expect(ChargeCodeResolver::slugForPosPaymentLabel('CARD'))->toBe('credit_card');
    expect(ChargeCodeResolver::slugForPosPaymentLabel('CREDIT_CARD'))->toBe('credit_card');
    expect(ChargeCodeResolver::slugForPosPaymentLabel('ACCOUNT_CHARGE'))->toBe('account_charge');
    expect(ChargeCodeResolver::slugForPosPaymentLabel('CASH'))->toBe('cash');
    expect(ChargeCodeResolver::slugForPosPaymentLabel('SOMETHING_ELSE'))->toBe('cash');
});
