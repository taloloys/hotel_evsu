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
                'description' => 'ROOM CHARGE',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 101,
                'description' => 'GOVERNMENT TAX',
                'category' => 'TAX_SERVICE',
            ],
            [
                'charge_code' => 102,
                'description' => 'SERVICE CHARGE PAYABLE',
                'category' => 'TAX_SERVICE',
            ],
            [
                'charge_code' => 103,
                'description' => 'EXTRA PAX',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 104,
                'description' => 'LAUNDRY SERVICE AND PRES',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 105,
                'description' => 'PRESSING',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 106,
                'description' => 'INCOMING FAX',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 107,
                'description' => 'OUTGOING FAX',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 108,
                'description' => 'FUNCTION ROOM',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 109,
                'description' => 'LONG DISTANCE-IDD',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 110,
                'description' => 'LONG DISTANCE-NDD',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 114,
                'description' => 'TRANSFER BALANCE',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 115,
                'description' => 'OTHER CHARGES',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 200,
                'description' => 'FOOD & BEVERAGE',
                'category' => 'RESTAURANT',
            ],
            [
                'charge_code' => 201,
                'description' => 'COMPLIMENTARY',
                'category' => 'HOTEL',
            ],
            [
                'charge_code' => 401,
                'description' => 'MASTERCARD',
                'category' => 'PAYMENT',
            ],
            [
                'charge_code' => 402,
                'description' => 'VISA',
                'category' => 'PAYMENT',
            ],
            [
                'charge_code' => 403,
                'description' => 'CASH',
                'category' => 'PAYMENT',
            ],
        ];

        foreach ($chargeCodes as $code) {
            ChargeCode::firstOrCreate(
                ['charge_code' => $code['charge_code']],
                [
                    'description' => $code['description'],
                    'category' => $code['category'],
                    'is_active' => true,
                ]
            );
        }
    }
}
