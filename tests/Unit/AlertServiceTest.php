<?php

namespace Tests\Unit;

use App\Services\AlertService;
use App\Services\AlertBag;
use Tests\TestCase;

class AlertServiceTest extends TestCase
{
    protected AlertService $alertService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->alertService = app(AlertService::class);
    }

    public function test_can_add_success_alert()
    {
        $this->alertService->success('Operation successful');

        $this->assertTrue($this->alertService->has('success'));
        $this->assertEquals(1, $this->alertService->countByType('success'));
    }

    public function test_can_add_warning_alert()
    {
        $this->alertService->warning('Warning message');

        $this->assertTrue($this->alertService->has('warning'));
        $this->assertEquals(1, $this->alertService->countByType('warning'));
    }

    public function test_can_add_error_alert()
    {
        $this->alertService->error('Error occurred');

        $this->assertTrue($this->alertService->has('error'));
        $this->assertEquals(1, $this->alertService->countByType('error'));
    }

    public function test_can_add_alert_with_code()
    {
        $this->alertService->success('Success', 'USER_CREATED');

        $successes = $this->alertService->getSuccesses();
        $this->assertEquals('USER_CREATED', $successes[0]['code']);
    }

    public function test_can_count_all_alerts()
    {
        $this->alertService->success('Success');
        $this->alertService->warning('Warning');
        $this->alertService->error('Error');

        $this->assertEquals(3, $this->alertService->count());
    }

    public function test_can_get_alerts_by_type()
    {
        $this->alertService->success('Success 1');
        $this->alertService->success('Success 2');
        $this->alertService->error('Error 1');

        $successes = $this->alertService->getSuccesses();
        $errors = $this->alertService->getErrors();

        $this->assertCount(2, $successes);
        $this->assertCount(1, $errors);
    }

    public function test_can_get_all_alerts()
    {
        $this->alertService->success('Success');
        $this->alertService->warning('Warning');
        $this->alertService->error('Error');

        $all = $this->alertService->all();

        $this->assertCount(1, $all['success']);
        $this->assertCount(1, $all['warning']);
        $this->assertCount(1, $all['error']);
    }

    public function test_can_delete_alert_by_id()
    {
        $this->alertService->success('Alert 1');
        $this->alertService->success('Alert 2');

        $successes = $this->alertService->getSuccesses();
        $idToDelete = $successes[0]['id'];

        $this->alertService->deleteSuccess($idToDelete);

        $this->assertEquals(1, $this->alertService->countByType('success'));
    }

    public function test_can_delete_alert_by_any_id()
    {
        $this->alertService->success('Success');
        $this->alertService->warning('Warning');

        $all = $this->alertService->all();
        $idToDelete = $all['success'][0]['id'];

        $this->alertService->delete($idToDelete);

        $this->assertEquals(0, $this->alertService->countByType('success'));
        $this->assertEquals(1, $this->alertService->countByType('warning'));
    }

    public function test_can_clear_all_alerts()
    {
        $this->alertService->success('Success');
        $this->alertService->warning('Warning');
        $this->alertService->error('Error');

        $this->alertService->clear();

        $this->assertEquals(0, $this->alertService->count());
    }

    public function test_can_clear_alerts_by_type()
    {
        $this->alertService->success('Success');
        $this->alertService->warning('Warning');
        $this->alertService->error('Error');

        $this->alertService->clearType('warning');

        $this->assertEquals(2, $this->alertService->count());
        $this->assertFalse($this->alertService->has('warning'));
    }

    public function test_can_flash_alerts()
    {
        $this->alertService->success('Success');
        $this->alertService->error('Error');

        $alerts = $this->alertService->flash();

        $this->assertCount(1, $alerts['success']);
        $this->assertCount(1, $alerts['error']);
        $this->assertEquals(0, $this->alertService->count());
    }

    public function test_can_peek_alerts_without_clearing()
    {
        $this->alertService->success('Success');
        $this->alertService->error('Error');

        $alerts1 = $this->alertService->peek();
        $alerts2 = $this->alertService->peek();

        $this->assertEquals($alerts1, $alerts2);
        $this->assertEquals(2, $this->alertService->count());
    }

    public function test_can_check_if_has_any_alerts()
    {
        $this->assertFalse($this->alertService->hasAny());

        $this->alertService->success('Success');

        $this->assertTrue($this->alertService->hasAny());
    }

    public function test_can_check_if_has_alerts_of_type()
    {
        $this->alertService->error('Error');

        $this->assertFalse($this->alertService->has('success'));
        $this->assertTrue($this->alertService->has('error'));
    }

    public function test_can_chain_alerts()
    {
        $this->alertService
            ->success('Success 1')
            ->success('Success 2')
            ->warning('Warning')
            ->error('Error');

        $this->assertEquals(4, $this->alertService->count());
    }

    public function test_can_convert_to_json()
    {
        $this->alertService->success('Success');
        $this->alertService->error('Error');

        $json = $this->alertService->toJson();
        $decoded = json_decode($json, true);

        $this->assertCount(1, $decoded['success']);
        $this->assertCount(1, $decoded['error']);
    }

    public function test_can_get_alert_bag_instance()
    {
        $bag = $this->alertService->bag();

        $this->assertInstanceOf(AlertBag::class, $bag);
    }

    public function test_alerts_persisted_in_session()
    {
        $this->alertService->success('Success');

        $this->assertTrue(session()->has('_alerts'));

        $stored = session()->get('_alerts');
        $this->assertCount(1, $stored['success']);
    }
}
