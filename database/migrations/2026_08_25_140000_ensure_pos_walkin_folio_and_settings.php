<?php

use App\Models\Folio;
use App\Models\Guest;
use App\Models\PosSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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

        PosSetting::set('walk_in_folio_id', (string) $walkInFolio->folio_id);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep system walkin folio intact
    }
};
