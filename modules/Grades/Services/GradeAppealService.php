<?php

namespace Modules\Grades\Services;

use Modules\Grades\Repositories\GradeAppealRepository;
use Modules\Grades\Models\GradeAppeal;

class GradeAppealService
{
    public function __construct(protected GradeAppealRepository $repository)
    {
    }

    public function submitAppeal(array $data): GradeAppeal
    {
        return $this->repository->create($data);
    }

    public function approveAppeal($id, $reviewedBy, $response = null): bool
    {
        return $this->repository->update($id, [
            'status' => 'approved',
            'response' => $response,
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
        ]);
    }

    public function rejectAppeal($id, $reviewedBy, $response = null): bool
    {
        return $this->repository->update($id, [
            'status' => 'rejected',
            'response' => $response,
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
        ]);
    }

    public function deleteAppeal($id): bool
    {
        return $this->repository->delete($id);
    }

    public function getStudentAppeals($studentId, $status = null)
    {
        return $this->repository->getStudentAppeals($studentId, $status);
    }

    public function getPendingAppeals($perPage = 25)
    {
        return $this->repository->getPendingAppeals($perPage);
    }
}
