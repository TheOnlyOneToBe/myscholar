<?php

namespace Modules\Config\Tests\Unit;

use Tests\TestCase;
use Modules\Config\Models\AcademicPeriod;
use Modules\Config\Services\AcademicTermService;
use Carbon\Carbon;

class AcademicTermServiceTest extends TestCase
{
    protected AcademicTermService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AcademicTermService::class);

        // Créer des trimestres de test
        $this->createTestTerms();
    }

    protected function createTestTerms(): void
    {
        $year = now()->year;

        AcademicPeriod::create([
            'name' => 'Trimestre 1',
            'type' => 'term',
            'start_date' => "$year-01-01",
            'end_date' => "$year-03-31",
            'academic_year' => $year,
            'order' => 1,
            'is_active' => true,
        ]);

        AcademicPeriod::create([
            'name' => 'Trimestre 2',
            'type' => 'term',
            'start_date' => "$year-04-01",
            'end_date' => "$year-07-31",
            'academic_year' => $year,
            'order' => 2,
            'is_active' => true,
        ]);

        AcademicPeriod::create([
            'name' => 'Trimestre 3',
            'type' => 'term',
            'start_date' => "$year-08-01",
            'end_date' => "$year-12-31",
            'academic_year' => $year,
            'order' => 3,
            'is_active' => true,
        ]);
    }

    public function test_get_terms_for_year(): void
    {
        $year = now()->year;
        $terms = $this->service->getTermsForYear($year);

        $this->assertCount(3, $terms);
        $this->assertEquals('Trimestre 1', $terms[0]->name);
        $this->assertEquals('Trimestre 2', $terms[1]->name);
        $this->assertEquals('Trimestre 3', $terms[2]->name);
    }

    public function test_get_formatted_terms(): void
    {
        $year = now()->year;
        $formatted = $this->service->getFormattedTerms($year);

        $this->assertIsArray($formatted);
        $this->assertArrayHasKey('term_1', $formatted);
        $this->assertArrayHasKey('term_2', $formatted);
        $this->assertArrayHasKey('term_3', $formatted);

        $this->assertEquals('Trimestre 1', $formatted['term_1']['name']);
        $this->assertEquals('Trimestre 2', $formatted['term_2']['name']);
        $this->assertEquals('Trimestre 3', $formatted['term_3']['name']);
    }

    public function test_get_term_by_number(): void
    {
        $year = now()->year;
        $term = $this->service->getTermByNumber(1, $year);

        $this->assertNotNull($term);
        $this->assertEquals('Trimestre 1', $term->name);
        $this->assertEquals(1, $term->order);
    }

    public function test_get_term_by_number_not_found(): void
    {
        $year = now()->year;
        $term = $this->service->getTermByNumber(99, $year);

        $this->assertNull($term);
    }

    public function test_get_current_term(): void
    {
        $year = now()->year;
        $currentMonth = now()->month;

        $current = $this->service->getCurrentTerm();

        if ($currentMonth >= 1 && $currentMonth <= 3) {
            $this->assertEquals('Trimestre 1', $current->name);
        } elseif ($currentMonth >= 4 && $currentMonth <= 7) {
            $this->assertEquals('Trimestre 2', $current->name);
        } elseif ($currentMonth >= 8 && $currentMonth <= 12) {
            $this->assertEquals('Trimestre 3', $current->name);
        }
    }

    public function test_get_next_term(): void
    {
        $next = $this->service->getNextTerm();

        if (now()->month < 3) {
            $this->assertEquals('Trimestre 1', $next->name);
        }
    }

    public function test_get_active_terms(): void
    {
        $year = now()->year;
        $activeTerms = $this->service->getActiveTerms($year);

        $this->assertCount(3, $activeTerms);
        $this->assertTrue($activeTerms->every(fn($term) => $term->is_active));
    }

    public function test_get_term_for_date(): void
    {
        $date = Carbon::parse(now()->year . '-02-15'); // 15 février
        $term = $this->service->getTermForDate($date);

        $this->assertNotNull($term);
        $this->assertEquals('Trimestre 1', $term->name);
    }

    public function test_initialize_default_terms(): void
    {
        $testYear = 2025;

        // Supprimer les termes existants pour le test
        AcademicPeriod::where('academic_year', $testYear)->delete();

        // Initialiser les termes par défaut
        $this->service->initializeDefaultTerms($testYear);

        // Vérifier qu'ils ont été créés
        $terms = AcademicPeriod::where('academic_year', $testYear)
            ->where('type', 'term')
            ->orderBy('order')
            ->get();

        $this->assertCount(3, $terms);
        $this->assertEquals('Trimestre 1', $terms[0]->name);
        $this->assertEquals('Trimestre 2', $terms[1]->name);
        $this->assertEquals('Trimestre 3', $terms[2]->name);
    }

    public function test_update_term_dates(): void
    {
        $year = now()->year;
        $newStart = Carbon::parse("$year-01-15");
        $newEnd = Carbon::parse("$year-04-15");

        $success = $this->service->updateTermDates(1, $newStart, $newEnd, $year);

        $this->assertTrue($success);

        $term = $this->service->getTermByNumber(1, $year);
        $this->assertEquals($newStart->format('Y-m-d'), $term->start_date->format('Y-m-d'));
        $this->assertEquals($newEnd->format('Y-m-d'), $term->end_date->format('Y-m-d'));
    }

    public function test_update_term_dates_not_found(): void
    {
        $year = now()->year;
        $success = $this->service->updateTermDates(99, now(), now()->addDays(90), $year);

        $this->assertFalse($success);
    }

    public function test_set_term_active(): void
    {
        $year = now()->year;

        // Désactiver le trimestre 1
        $success = $this->service->setTermActive(1, false, $year);
        $this->assertTrue($success);

        $term = $this->service->getTermByNumber(1, $year);
        $this->assertFalse($term->is_active);

        // Le réactiver
        $success = $this->service->setTermActive(1, true, $year);
        $this->assertTrue($success);

        $term = $this->service->getTermByNumber(1, $year);
        $this->assertTrue($term->is_active);
    }

    public function test_set_term_active_not_found(): void
    {
        $year = now()->year;
        $success = $this->service->setTermActive(99, false, $year);

        $this->assertFalse($success);
    }

    public function test_clear_cache(): void
    {
        $year = now()->year;
        $cacheKey = "academic_terms_year_{$year}";

        // Remplir le cache
        $this->service->getTermsForYear($year);

        // Vérifier que le cache existe
        $this->assertNotNull(\Illuminate\Support\Facades\Cache::get($cacheKey));

        // Effacer le cache
        $this->service->clearCache($year);

        // Vérifier que le cache est vide
        $this->assertNull(\Illuminate\Support\Facades\Cache::get($cacheKey));
    }
}
