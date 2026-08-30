<?php

namespace App\Services;

use App\Models\ChargeCode;
use Illuminate\Support\Facades\Log;

/**
 * Central registry for system-known charge code slugs and dynamic resolution.
 *
 * Every module should reference charge codes through this service using the
 * slug constants below — never by hardcoded numeric IDs. The resolver looks
 * up the current integer charge_code from the `chargecodes` table at runtime.
 */
class ChargeCodeResolver
{
    // ── Hotel charges ────────────────────────────────────────────
    const ROOM_CHARGE = 'room_charge';

    const GOV_TAX = 'gov_tax';

    const SERVICE_CHARGE = 'service_charge';

    // ── Restaurant / F&B ─────────────────────────────────────────
    const FOOD_BEVERAGE = 'food_beverage';

    // ── Payment methods ──────────────────────────────────────────
    const CASH = 'cash';

    const GCASH = 'gcash';

    const MAYA = 'maya';

    const CREDIT_CARD = 'credit_card';

    const ACCOUNT_CHARGE = 'account_charge';

    /**
     * Map a user-facing payment method name to its corresponding slug.
     */
    const PAYMENT_METHOD_SLUGS = [
        'Cash' => self::CASH,
        'Credit Card' => self::CREDIT_CARD,
        'GCash' => self::GCASH,
        'Maya' => self::MAYA,
    ];

    /**
     * Map a user-facing payment method name to the internal transaction payment_method value.
     */
    const PAYMENT_METHOD_VALUES = [
        'Cash' => 'CASH',
        'Credit Card' => 'CREDIT_CARD',
        'GCash' => 'GCASH',
        'Maya' => 'MAYA',
    ];

    /**
     * Resolve the integer charge_code for a given slug.
     *
     * Returns null and logs a warning if the slug is not found or inactive.
     * Callers should check for null and handle gracefully (e.g. return a
     * user-facing error message instead of crashing with an FK violation).
     */
    public static function resolve(string $slug): ?int
    {
        $code = ChargeCode::where('slug', $slug)
            ->where('is_active', true)
            ->value('charge_code');

        if ($code === null) {
            Log::warning("ChargeCodeResolver: No active charge code found for slug [{$slug}].", [
                'slug' => $slug,
            ]);
        }

        return $code;
    }

    /**
     * Resolve a payment method name (e.g. "GCash") to its integer charge_code.
     *
     * Returns null if the payment method is unknown or its charge code is missing.
     */
    public static function resolvePaymentMethod(string $paymentMethod): ?int
    {
        $slug = self::PAYMENT_METHOD_SLUGS[$paymentMethod] ?? null;

        if ($slug === null) {
            Log::warning("ChargeCodeResolver: Unknown payment method [{$paymentMethod}].", [
                'payment_method' => $paymentMethod,
            ]);

            return null;
        }

        return self::resolve($slug);
    }

    /**
     * Map a POS payment method label to its charge code slug.
     */
    public static function slugForPosPaymentLabel(string $label): string
    {
        return match (strtoupper($label)) {
            'CARD', 'CREDIT_CARD' => self::CREDIT_CARD,
            'GCASH' => self::GCASH,
            'MAYA' => self::MAYA,
            'ACCOUNT_CHARGE' => self::ACCOUNT_CHARGE,
            default => self::CASH,
        };
    }
}
