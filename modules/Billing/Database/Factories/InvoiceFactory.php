<?php

namespace Modules\Billing\Database\Factories;

use Modules\Billing\Models\Invoice;
use Modules\Students\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'invoice_number' => 'INV-' . $this->faker->unique()->numerify('######'),
            'student_id' => Student::factory(),
            'amount' => $this->faker->randomFloat(2, 10000, 500000),
            'amount_paid' => 0,
            'currency' => 'FCFA',
            'issue_date' => now()->subDays(30),
            'due_date' => now()->addDays(30),
            'status' => 'pending',
            'notes' => $this->faker->sentence(),
        ];
    }
}
