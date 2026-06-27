<?php

namespace Modules\Config\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Config\Models\SchoolYear;
use Modules\Config\Services\SchoolYearSessionService;

/**
 * School Year Session Controller
 * Manages switching between school years in session
 */
class SchoolYearSessionController extends Controller
{
    public function __construct(private SchoolYearSessionService $sessionService)
    {
    }

    /**
     * Get current active school year in session
     */
    public function current(): array
    {
        $this->authorize('config.school_year.view');

        $year = $this->sessionService->getActiveYear();

        return [
            'id' => $year?->id,
            'name' => $year?->name,
            'start_date' => $year?->start_date,
            'end_date' => $year?->end_date,
            'is_active' => $year?->is_active,
            'is_locked' => $year?->is_locked,
        ];
    }

    /**
     * List all available school years
     */
    public function index(): array
    {
        $this->authorize('config.school_year.view');

        $years = SchoolYear::orderBy('start_date', 'desc')->get();

        return [
            'current_year_id' => $this->sessionService->getActiveYearId(),
            'years' => $years->map(fn($year) => [
                'id' => $year->id,
                'name' => $year->name,
                'start_date' => $year->start_date,
                'end_date' => $year->end_date,
                'is_active' => $year->is_active,
                'is_locked' => $year->is_locked,
                'can_modify' => $this->sessionService->canModifyYear($year),
                'in_session' => $year->id === $this->sessionService->getActiveYearId(),
            ])->toArray(),
        ];
    }

    /**
     * Switch to a different school year in session
     */
    public function switch(Request $request): array
    {
        $this->authorize('config.school_year.switch');

        $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        $year = $this->sessionService->setActiveYearById($request->school_year_id);

        return [
            'message' => trans('config.messages.school_year_switched', ['name' => $year->name]),
            'id' => $year->id,
            'name' => $year->name,
            'start_date' => $year->start_date,
            'end_date' => $year->end_date,
            'can_modify' => $this->sessionService->canModifyYear($year),
        ];
    }

    /**
     * Get school year info with permission details
     */
    public function info(SchoolYear $schoolYear): array
    {
        $this->authorize('config.school_year.view');

        return [
            'id' => $schoolYear->id,
            'name' => $schoolYear->name,
            'start_date' => $schoolYear->start_date,
            'end_date' => $schoolYear->end_date,
            'is_active' => $schoolYear->is_active,
            'is_locked' => $schoolYear->is_locked,
            'can_modify' => $this->sessionService->canModifyYear($schoolYear),
            'is_current_session' => $schoolYear->id === $this->sessionService->getActiveYearId(),
            'message' => $this->getYearStatusMessage($schoolYear),
        ];
    }

    /**
     * Get human-readable status message for a school year
     */
    private function getYearStatusMessage(SchoolYear $year): string
    {
        if ($year->is_locked) {
            return trans('config.errors.cannot_modify_locked_year');
        }

        if (!$this->sessionService->canModifyYear($year)) {
            return trans('config.errors.permission_denied_past_year');
        }

        if ($year->id === $this->sessionService->getActiveYearId()) {
            return trans('config.messages.active_school_year');
        }

        return 'Available for viewing';
    }
}
