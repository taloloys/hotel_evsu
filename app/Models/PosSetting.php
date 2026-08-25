<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosSetting extends Model
{
    protected $primaryKey = 'setting_key';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'updated_at',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::find($key);

        return $setting?->setting_value ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value, 'updated_at' => now()]
        );
    }

    public static function defaultLowStockThreshold(): int
    {
        return (int) (static::get('default_low_stock_threshold', '10') ?? 10);
    }

    public static function walkInFolioId(): int
    {
        $value = static::get('walk_in_folio_id');

        if ($value !== null && Folio::where('folio_id', (int) $value)->exists()) {
            return (int) $value;
        }

        $walkInFolio = Folio::where('folio_number', 'POS-WALKIN')->first();

        if (! $walkInFolio) {
            $walkInGuest = Guest::firstOrCreate(
                ['last_name' => 'WALK-IN', 'first_name' => 'POS'],
                ['contact_number' => 'N/A', 'guest_type' => 'SYSTEM']
            );

            $walkInFolio = Folio::firstOrCreate(
                ['folio_number' => 'POS-WALKIN'],
                [
                    'guest_id' => $walkInGuest->guest_id,
                    'folio_type' => 'SYSTEM',
                    'status' => 'OPEN',
                    'market_segment' => 'NONE',
                    'symbol' => 'POS',
                ]
            );
        }

        static::set('walk_in_folio_id', (string) $walkInFolio->folio_id);

        return (int) $walkInFolio->folio_id;
    }
}
