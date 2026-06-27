<?php

namespace Tests\Feature\Config;

use Modules\Auth\Models\User;
use Modules\Config\Models\SchoolYear;
use Tests\TestCase;

class SchoolYearRouteBindingTest extends TestCase
{
    public function test_get_school_year_via_route()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $schoolYear = SchoolYear::create([
            'name' => '2024-2025-route',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-09-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        // Verify model was created
        $this->assertNotNull($schoolYear->id, 'Model should have an ID');
        $this->assertTrue($schoolYear->is_active, 'Model should be active');

        // Try to GET the model via route
        $response = $this->get("/api/config/school-years/{$schoolYear->id}");

        $this->assertEquals(200, $response->status(), "Should return 200 for existing model. Response: " . $response->getContent());

        $data = $response->json();
        \Log::debug('Response from GET', ['response' => $data]);

        $this->assertIsArray($response->json('data'), 'Should return data array');
        $this->assertEquals($schoolYear->id, $response->json('data.id'), 'Should return correct ID');
    }
}
