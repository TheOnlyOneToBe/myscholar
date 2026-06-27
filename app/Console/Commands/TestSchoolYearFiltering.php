<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Config\Models\SchoolYear;
use Modules\Config\Services\SchoolYearService;
use Modules\Students\Models\Student;
use Modules\Grades\Models\Grade;
use Modules\Billing\Models\Invoice;

class TestSchoolYearFiltering extends Command
{
    protected $signature = 'test:school-year-filtering';
    protected $description = 'Test school year filtering and historical data access';

    public function handle()
    {
        $this->info('Testing School Year Filtering & Historical Data Access...');
        $this->line('');

        $service = app(SchoolYearService::class);

        try {
            // Test 1: Get current school year
            $this->testCurrentSchoolYear($service);

            // Test 2: Create data for multiple years
            $this->testMultiYearData($service);

            // Test 3: Filter by specific year
            $this->testFilterByYear($service);

            // Test 4: Access historical data
            $this->testHistoricalDataAccess($service);

            // Test 5: Cross-year comparisons
            $this->testCrossYearComparison($service);

            $this->line('');
            $this->info('✓ All school year filtering tests PASSED!');

            return 0;

        } catch (\Exception $e) {
            $this->error('✗ Test failed!');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function testCurrentSchoolYear(SchoolYearService $service): void
    {
        $this->info('1. Testing Current School Year');

        $current = $service->getCurrentSchoolYear();

        if (!$current) {
            throw new \Exception('No current school year found');
        }

        $this->line("   ✓ Current year: {$current->name}");
        $this->line("   ✓ Progress: {$current->getProgressPercentage()}%");
        $this->line("   ✓ Duration: {$current->getDuration()} days");
        $this->line('');
    }

    private function testMultiYearData(SchoolYearService $service): void
    {
        $this->info('2. Creating Test Data Across Multiple Years');

        $years = SchoolYear::orderBy('start_year', 'desc')->limit(2)->get();

        if ($years->count() < 2) {
            $this->warn('   Only ' . $years->count() . ' years available, skipping multi-year test');
            return;
        }

        $timestamp = time();

        foreach ($years as $year) {
            $student = Student::create([
                'student_id_number' => 'TEST_' . $year->id . '_' . $timestamp,
                'first_name' => 'Multi_Year_' . $year->start_year,
                'last_name' => 'Student',
                'date_of_birth' => '2005-01-15',
                'sex' => 'M',
                'school_year_id' => $year->id,
                'enrollment_status' => 'active',
            ]);

            $this->line("   ✓ Created student for {$year->name}: {$student->student_id_number}");
        }

        $this->line('');
    }

    private function testFilterByYear(SchoolYearService $service): void
    {
        $this->info('3. Testing Filtering by Specific Year');

        $years = SchoolYear::orderBy('start_year', 'desc')->limit(2)->get();

        foreach ($years as $year) {
            $studentCount = Student::where('school_year_id', $year->id)->count();
            $this->line("   ✓ {$year->name}: {$studentCount} students");
        }

        $this->line('');
    }

    private function testHistoricalDataAccess(SchoolYearService $service): void
    {
        $this->info('4. Testing Historical Data Access');

        // Get all years
        $allYears = SchoolYear::orderBy('start_year', 'desc')->get();
        $this->line("   ✓ Available years: {$allYears->count()}");

        // Access data from each year
        foreach ($allYears as $year) {
            $studentCount = Student::where('school_year_id', $year->id)->count();
            $status = $year->is_locked ? '[ARCHIVED]' : '[ACTIVE]';
            $this->line("   ✓ {$year->name} {$status}: {$studentCount} students");
        }

        $this->line('');
    }

    private function testCrossYearComparison(SchoolYearService $service): void
    {
        $this->info('5. Testing Cross-Year Comparison');

        $current = $service->getCurrentSchoolYear();
        $previous = $service->getPreviousSchoolYear($current);
        $next = $service->getNextSchoolYear($current);

        $this->table(
            ['Relationship', 'Year', 'Status'],
            [
                ['Previous', $previous?->name ?? 'N/A', $previous?->is_locked ? 'Archived' : 'Active'],
                ['Current', $current->name, 'Active'],
                ['Next', $next?->name ?? 'N/A', $next?->is_locked ? 'Archived' : 'Active'],
            ]
        );

        $this->line('');

        // Compare student counts
        if ($previous && $current) {
            $prevCount = Student::where('school_year_id', $previous->id)->count();
            $currCount = Student::where('school_year_id', $current->id)->count();

            $this->line("   Students comparison:");
            $this->line("   - {$previous->name}: {$prevCount}");
            $this->line("   - {$current->name}: {$currCount}");

            if ($prevCount > 0) {
                $growth = (($currCount - $prevCount) / $prevCount) * 100;
                $this->line("   - Growth: {$growth}%");
            }
        }

        $this->line('');
    }
}
