<?php

namespace Tests\Feature\Config;

use Modules\Auth\Models\User;
use Modules\Config\Models\SchoolYear;
use Tests\TestCase;

class SchoolYearDebugTest extends TestCase
{
    public function test_school_year_is_active_persists()
    {
        // Create a school year with is_active = true
        $schoolYear = SchoolYear::create([
            'name' => '2024-2025-debug',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        // Check immediately
        $this->assertTrue($schoolYear->is_active, 'is_active should be true immediately after creation');

        // Refresh from database
        $schoolYear->refresh();
        $this->assertTrue($schoolYear->is_active, 'is_active should still be true after refresh');

        // Check from database
        $fromDb = SchoolYear::find($schoolYear->id);
        $this->assertTrue($fromDb->is_active, 'is_active should be true when fetched from database');
    }

    public function test_delete_active_school_year_via_api()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $schoolYear = SchoolYear::create([
            'name' => '2024-2025-api',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        // Verify it was created as active
        $fromDb = SchoolYear::find($schoolYear->id);
        $this->assertTrue($fromDb->is_active, 'SchoolYear should be active in database');

        // Try to delete
        $response = $this->delete("/api/config/school-years/{$schoolYear->id}");

        // Should get 422
        $this->assertTrue(
            in_array($response->status(), [400, 422]),
            "Expected 400 or 422 for active year, got {$response->status()}\nResponse: " . $response->getContent()
        );

        // Verify it still exists
        $this->assertDatabaseHas('school_years', ['id' => $schoolYear->id]);
    }
}
