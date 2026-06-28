<?php

namespace Modules\Config\Tests\Feature;

use Tests\TestCase;
use Modules\Auth\Models\User;
use Modules\Config\Models\AcademicPeriod;
use Carbon\Carbon;

class AcademicPeriodControllerTest extends TestCase
{
    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->giveRole('super_administrator');

        $this->user = User::factory()->create();
        $this->user->giveRole('student');

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
    }

    public function test_list_academic_periods_authenticated(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/config/academic-periods');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'type', 'start_date', 'end_date', 'academic_year', 'order']
                ],
                'year'
            ]);
    }

    public function test_list_academic_periods_unauthenticated(): void
    {
        $response = $this->getJson('/api/config/academic-periods');

        $response->assertStatus(401);
    }

    public function test_list_academic_periods_specific_year(): void
    {
        $year = 2024;
        $response = $this->actingAs($this->admin)
            ->getJson('/api/config/academic-periods?year=' . $year);

        $response->assertStatus(200)
            ->assertJson(['year' => $year]);
    }

    public function test_get_current_term(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/config/academic-periods/current');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'type', 'start_date', 'end_date', 'academic_year']
            ]);
    }

    public function test_get_specific_term(): void
    {
        $termNumber = 1;
        $response = $this->actingAs($this->admin)
            ->getJson('/api/config/academic-periods/' . $termNumber);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'type', 'start_date', 'end_date', 'academic_year']
            ])
            ->assertJsonPath('data.order', 1);
    }

    public function test_get_nonexistent_term(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/config/academic-periods/999');

        $response->assertStatus(404)
            ->assertJsonPath('error', 'Trimestre non trouvé');
    }

    public function test_update_term_dates(): void
    {
        $term = AcademicPeriod::where('order', 1)->first();
        $newStart = now()->year . '-02-01';
        $newEnd = now()->year . '-04-15';

        $response = $this->actingAs($this->admin)
            ->putJson('/api/config/academic-periods/' . $term->id, [
                'name' => 'Trimestre 1 Modifié',
                'start_date' => $newStart,
                'end_date' => $newEnd,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Trimestre 1 Modifié')
            ->assertJsonPath('data.start_date', $newStart);
    }

    public function test_update_term_with_invalid_dates(): void
    {
        $term = AcademicPeriod::where('order', 1)->first();

        $response = $this->actingAs($this->admin)
            ->putJson('/api/config/academic-periods/' . $term->id, [
                'start_date' => now()->year . '-04-15',
                'end_date' => now()->year . '-02-01', // Date fin avant date début
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_update_nonexistent_term(): void
    {
        $response = $this->actingAs($this->admin)
            ->putJson('/api/config/academic-periods/999', [
                'name' => 'Test',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(90)->format('Y-m-d'),
            ]);

        $response->assertStatus(404)
            ->assertJsonPath('error', 'Trimestre non trouvé');
    }

    public function test_initialize_default_terms(): void
    {
        $testYear = 2025;

        // Supprimer les termes existants pour le test
        AcademicPeriod::where('academic_year', $testYear)->delete();

        $response = $this->actingAs($this->admin)
            ->postJson('/api/config/academic-periods/initialize', [
                'year' => $testYear
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', "Trimestres initialisés pour l'année $testYear")
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'start_date', 'end_date']
                ]
            ]);

        // Vérifier que 3 trimestres ont été créés
        $terms = AcademicPeriod::where('academic_year', $testYear)->count();
        $this->assertEquals(3, $terms);
    }

    public function test_unauthorized_user_cannot_update_term(): void
    {
        $term = AcademicPeriod::where('order', 1)->first();

        $response = $this->actingAs($this->user)
            ->putJson('/api/config/academic-periods/' . $term->id, [
                'name' => 'Trimestre Modifié',
            ]);

        // Should be forbidden or require specific permission
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_initialize_terms(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/config/academic-periods/initialize', [
                'year' => 2025
            ]);

        $response->assertStatus(403);
    }
}
