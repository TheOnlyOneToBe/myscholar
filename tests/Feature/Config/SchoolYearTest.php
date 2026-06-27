<?php

namespace Tests\Feature\Config;

use Modules\Auth\Models\User;
use Modules\Config\Models\SchoolYear;
use Tests\TestCase;

class SchoolYearTest extends TestCase
{
    public function test_can_create_school_year()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/api/config/school-years', [
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'description' => 'Academic year 2024-2025',
        ]);

        if ($response->status() !== 201) {
            $this->fail("Expected status 201, got {$response->status()}. Response: " . $response->getContent());
        }

        $this->assertDatabaseHas('school_years', ['name' => '2024-2025']);
    }

    public function test_can_view_school_years()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        SchoolYear::create([
            'name' => '2023-2024',
            'start_year' => 2023,
            'end_year' => 2024,
            'start_date' => '2023-09-01',
            'end_date' => '2024-06-30',
        ]);

        $response = $this->get('/api/config/school-years');
        $response->assertStatus(200);
    }

    public function test_can_get_current_school_year()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        $response = $this->get('/api/config/school-years/current');
        $response->assertStatus(200);
        $response->assertJsonPath('data.name', '2024-2025');
    }

    public function test_cannot_delete_active_school_year()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $schoolYear = SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        $response = $this->delete("/api/config/school-years/{$schoolYear->id}");
        $response->assertStatus(422);
    }

    public function test_can_activate_school_year()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $year1 = SchoolYear::create([
            'name' => '2023-2024',
            'start_year' => 2023,
            'end_year' => 2024,
            'start_date' => '2023-09-01',
            'end_date' => '2024-06-30',
            'is_active' => true,
        ]);

        $year2 = SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => false,
        ]);

        $response = $this->post("/api/config/school-years/{$year2->id}/activate");
        $response->assertStatus(200);

        $this->assertTrue($year2->fresh()->is_active);
        $this->assertFalse($year1->fresh()->is_active);
    }

    public function test_school_year_requires_end_date_after_start_date()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/api/config/school-years', [
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2025-06-30',
            'end_date' => '2024-09-01',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('end_date');
    }

    public function test_school_year_must_have_unique_year()
    {
        SchoolYear::create([
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/api/config/school-years', [
            'name' => '2024-2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }
}
