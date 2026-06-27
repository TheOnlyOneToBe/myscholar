<?php

namespace Tests\Feature\Config;

use Modules\Auth\Models\User;
use Modules\Config\Models\SchoolInfo;
use Tests\TestCase;

class SchoolInfoTest extends TestCase
{
    public function test_can_view_school_info()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $school = SchoolInfo::create([
            'name' => 'Lycée Test',
            'acronym' => 'LT',
            'school_type' => 'public',
            'city' => 'Douala',
        ]);

        $response = $this->get('/api/config/school');
        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Lycée Test');
    }

    public function test_can_update_school_info()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole(
            \Modules\Auth\Models\Role::firstOrCreate(['name' => 'admin'])
        );

        $this->actingAs($admin);

        $response = $this->put('/api/config/school', [
            'name' => 'Lycée Cameroun',
            'acronym' => 'LC',
            'school_type' => 'prive',
            'city' => 'Yaoundé',
            'address' => '123 Rue Principale',
            'email' => 'contact@lycee.cm',
            'phone' => '+237 123456789',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('school_info', ['name' => 'Lycée Cameroun']);
    }

    public function test_school_info_returns_404_when_not_configured()
    {
        SchoolInfo::truncate();
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/api/config/school');
        $response->assertStatus(404);
    }

    public function test_can_get_contact_info()
    {
        $school = SchoolInfo::create([
            'name' => 'Lycée Test',
            'email' => 'contact@test.cm',
            'phone' => '+237 123456789',
            'website' => 'https://lycee.cm',
            'po_box' => '12345',
        ]);

        $contactInfo = $school->getContactInfo();

        $this->assertEquals('contact@test.cm', $contactInfo['email']);
        $this->assertEquals('+237 123456789', $contactInfo['phone']);
    }

    public function test_can_get_full_name()
    {
        $school = SchoolInfo::create([
            'name' => 'Lycée Test',
            'acronym' => 'LT',
        ]);

        $this->assertEquals('Lycée Test (LT)', $school->getFullName());
    }

    public function test_can_get_full_address()
    {
        $school = SchoolInfo::create([
            'name' => 'Lycée Test',
            'address' => '123 Rue Principale',
            'city' => 'Douala',
            'region' => 'Littoral',
        ]);

        $this->assertStringContainsString('123 Rue Principale', $school->getFullAddress());
        $this->assertStringContainsString('Douala', $school->getFullAddress());
        $this->assertStringContainsString('Littoral', $school->getFullAddress());
    }

    public function test_has_logo_detection()
    {
        $schoolWithLogo = SchoolInfo::create([
            'name' => 'Lycée Test',
            'logo_path' => 'logos/test.png',
        ]);

        $schoolWithoutLogo = SchoolInfo::create([
            'name' => 'Lycée Test 2',
        ]);

        $this->assertTrue($schoolWithLogo->hasLogo());
        $this->assertFalse($schoolWithoutLogo->hasLogo());
    }
}
