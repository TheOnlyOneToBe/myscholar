<?php

namespace Modules\Billing\Database\Factories;

use Modules\Billing\Models\Payment;
use Modules\Billing\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'student_id' => null,
            'payment_date' => now(),
            'amount' => $this->faker->randomFloat(2, 10000, 500000),
            'payment_method' => $this->faker->randomElement(['cash', 'check', 'virement', 'mobile_money', 'card']),
            'reference_number' => $this->faker->unique()->numerify('REF-######'),
        ];
    }
}
