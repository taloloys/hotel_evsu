<?php

namespace Database\Seeders;

use App\Models\ChargeCode;
use Illuminate\Database\Seeder;

class ChargeCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chargeCodes = [
            [
                'charge_code' => 100,
                'slug' => 'room_charge',
                'description' => 'ROOM CHARGE',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 101,
                'slug' => 'gov_tax',
                'description' => 'GOVERNMENT TAX',
                'category' => 'TAX_SERVICE',
            ],
            [
                'charge_code' => 102,
                'slug' => 'service_charge',
                'description' => 'SERVICE CHARGE PAYABLE',
                'category' => 'TAX_SERVICE',
            ],
            [
                'charge_code' => 103,
                'slug' => 'extra_pax',
                'description' => 'EXTRA PAX',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 104,
                'slug' => 'laundry_service',
                'description' => 'LAUNDRY SERVICE AND PRES',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 105,
                'slug' => 'pressing',
                'description' => 'PRESSING',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 106,
                'slug' => 'incoming_fax',
                'description' => 'INCOMING FAX',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 107,
                'slug' => 'outgoing_fax',
                'description' => 'OUTGOING FAX',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 108,
                'slug' => 'function_room',
                'description' => 'FUNCTION ROOM',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 109,
                'slug' => 'long_distance_idd',
                'description' => 'LONG DISTANCE-IDD',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 110,
                'slug' => 'long_distance_ndd',
                'description' => 'LONG DISTANCE-NDD',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 114,
                'slug' => 'transfer_balance',
                'description' => 'TRANSFER BALANCE',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 115,
                'slug' => 'other_charges',
                'description' => 'OTHER CHARGES',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 200,
                'slug' => 'food_beverage',
                'description' => 'FOOD & BEVERAGE',
                'category' => 'RESTAURANT',
            ],
            [
                'charge_code' => 201,
                'slug' => 'complimentary',
                'description' => 'COMPLIMENTARY',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 401,
                'slug' => 'credit_card',
                'description' => 'MASTERCARD',
                'category' => 'PAYMENT',
            ],
            [
                'charge_code' => 402,
                'slug' => 'visa',
                'description' => 'VISA',
                'category' => 'PAYMENT',
            ],
            [
                'charge_code' => 403,
                'slug' => 'cash',
                'description' => 'CASH',
                'category' => 'PAYMENT',
            ],
            [
                'charge_code' => 404,
                'slug' => 'account_charge',
                'description' => 'ACCOUNT CHARGE',
                'category' => 'PAYMENT',
            ],
            [
                'charge_code' => 405,
                'slug' => 'gcash',
                'description' => 'GCASH',
                'category' => 'PAYMENT',
            ],
            [
                'charge_code' => 406,
                'slug' => 'maya',
                'description' => 'MAYA',
                'category' => 'PAYMENT',
            ],
        ];

        foreach ($chargeCodes as $code) {
            ChargeCode::firstOrCreate(
                ['charge_code' => $code['charge_code']],
                [
                    'slug' => $code['slug'] ?? null,
                    'description' => $code['description'],
                    'category' => $code['category'],
                    'is_active' => true,
                ]
            );
        }
    }
}
