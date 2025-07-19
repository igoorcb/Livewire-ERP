<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cupom;

class CupomSeeder extends Seeder
{
    public function run()
    {
        Cupom::create([
            'codigo' => 'DESCONTO10',
            'validade' => now()->addMonths(3),
            'valor_minimo' => 50.00,
            'desconto' => 10.00,
            'tipo' => 'percentual',
        ]);

        Cupom::create([
            'codigo' => 'FREEGRATIS',
            'validade' => now()->addMonths(6),
            'valor_minimo' => 100.00,
            'desconto' => 25.00,
            'tipo' => 'fixo',
        ]);

        Cupom::create([
            'codigo' => 'PROMO50',
            'validade' => now()->addDays(30),
            'valor_minimo' => 200.00,
            'desconto' => 50.00,
            'tipo' => 'fixo',
        ]);
    }
}
