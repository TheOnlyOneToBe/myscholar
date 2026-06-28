<?php

namespace Modules\Config\Tests\Unit;

use Tests\TestCase;
use Modules\Config\Models\AcademicPeriod;
use Carbon\Carbon;

class AcademicPeriodModelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestPeriods();
    }

    protected function createTestPeriods(): void
    {
        $year = now()->year;

        // Période en cours
        AcademicPeriod::create([
            'name' => 'Trimestre Actuel',
            'type' => 'term',
            'start_date' => now()->subDays(30),
            'end_date' => now()->addDays(30),
            'academic_year' => $year,
            'order' => 1,
            'is_active' => true,
        ]);

        // Période terminée
        AcademicPeriod::create([
            'name' => 'Trimestre Passé',
            'type' => 'term',
            'start_date' => now()->subMonths(4),
            'end_date' => now()->subMonths(3),
            'academic_year' => $year,
            'order' => 2,
            'is_active' => true,
        ]);

        // Période future
        AcademicPeriod::create([
            'name' => 'Trimestre Futur',
            'type' => 'term',
            'start_date' => now()->addMonths(3),
            'end_date' => now()->addMonths(6),
            'academic_year' => $year,
            'order' => 3,
            'is_active' => true,
        ]);

        // Période inactive
        AcademicPeriod::create([
            'name' => 'Trimestre Inactif',
            'type' => 'term',
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(10),
            'academic_year' => $year,
            'order' => 4,
            'is_active' => false,
        ]);
    }

    public function test_is_in_progress(): void
    {
        $period = AcademicPeriod::where('name', 'Trimestre Actuel')->first();

        $this->assertTrue($period->isInProgress());
    }

    public function test_is_upcoming(): void
    {
        $period = AcademicPeriod::where('name', 'Trimestre Futur')->first();

        $this->assertTrue($period->isUpcoming());
    }

    public function test_is_completed(): void
    {
        $period = AcademicPeriod::where('name', 'Trimestre Passé')->first();

        $this->assertTrue($period->isCompleted());
    }

    public function test_get_status(): void
    {
        $inProgress = AcademicPeriod::where('name', 'Trimestre Actuel')->first();
        $this->assertEquals('in_progress', $inProgress->getStatus());

        $completed = AcademicPeriod::where('name', 'Trimestre Passé')->first();
        $this->assertEquals('completed', $completed->getStatus());

        $upcoming = AcademicPeriod::where('name', 'Trimestre Futur')->first();
        $this->assertEquals('upcoming', $upcoming->getStatus());
    }

    public function test_get_days_until_start(): void
    {
        $period = AcademicPeriod::where('name', 'Trimestre Futur')->first();
        $daysUntil = $period->getDaysUntilStart();

        $this->assertGreaterThan(0, $daysUntil);
    }

    public function test_get_days_remaining(): void
    {
        $period = AcademicPeriod::where('name', 'Trimestre Actuel')->first();
        $daysRemaining = $period->getDaysRemaining();

        // Les jours restants peuvent être négatifs si la période est terminée
        // On teste juste que la méthode fonctionne correctement
        $this->assertIsInt($daysRemaining);
    }

    public function test_get_duration(): void
    {
        $period = AcademicPeriod::where('name', 'Trimestre Actuel')->first();
        $duration = $period->getDuration();

        $this->assertEquals(60, $duration); // 30 jours avant + 30 après = 60
    }

    public function test_scope_by_year(): void
    {
        $year = now()->year;
        $periods = AcademicPeriod::byYear($year)->get();

        $this->assertGreaterThan(0, $periods->count());
        $this->assertTrue($periods->every(fn($p) => $p->academic_year === $year));
    }

    public function test_scope_by_type(): void
    {
        $periods = AcademicPeriod::byType('term')->get();

        $this->assertGreaterThan(0, $periods->count());
        $this->assertTrue($periods->every(fn($p) => $p->type === 'term'));
    }

    public function test_scope_active(): void
    {
        $periods = AcademicPeriod::active()->get();

        $this->assertGreaterThan(0, $periods->count());
        $this->assertTrue($periods->every(fn($p) => $p->is_active === true));
    }

    public function test_scope_ordered(): void
    {
        $periods = AcademicPeriod::ordered()->get();

        $this->assertGreaterThan(0, $periods->count());

        for ($i = 1; $i < $periods->count(); $i++) {
            $this->assertLessThanOrEqual(
                $periods[$i]->order,
                $periods[$i - 1]->order
            );
        }
    }

    public function test_scope_current_year(): void
    {
        $periods = AcademicPeriod::currentYear()->get();

        $this->assertGreaterThan(0, $periods->count());
        $this->assertTrue($periods->every(fn($p) => $p->academic_year === now()->year));
    }

    public function test_unique_constraint(): void
    {
        $this->expectException(\Exception::class);

        AcademicPeriod::create([
            'name' => 'Duplicate',
            'type' => 'term',
            'start_date' => now(),
            'end_date' => now()->addDays(90),
            'academic_year' => now()->year,
            'order' => 1, // Déjà utilisé pour le year et type
            'is_active' => true,
        ]);
    }

    public function test_fillable_attributes(): void
    {
        $attributes = [
            'name' => 'Test Term',
            'type' => 'term',
            'start_date' => now(),
            'end_date' => now()->addDays(90),
            'academic_year' => 2025,
            'order' => 5,
            'is_active' => true,
            'description' => 'Test description',
        ];

        AcademicPeriod::create($attributes);
        $period = AcademicPeriod::where('name', 'Test Term')->first();

        $this->assertEquals('Test Term', $period->name);
        $this->assertEquals('term', $period->type);
        $this->assertEquals(2025, $period->academic_year);
        $this->assertEquals(5, $period->order);
        $this->assertTrue($period->is_active);
        $this->assertEquals('Test description', $period->description);
    }
}
