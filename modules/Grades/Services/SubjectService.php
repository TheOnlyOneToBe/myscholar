<?php

namespace Modules\Grades\Services;

use Modules\Grades\Repositories\SubjectRepository;
use Modules\Grades\Models\Subject;

class SubjectService
{
    public function __construct(protected SubjectRepository $repository)
    {
    }

    public function createSubject(array $data): Subject
    {
        return $this->repository->create($data);
    }

    public function updateSubject($id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteSubject($id): bool
    {
        return $this->repository->delete($id);
    }

    public function getActiveSubjects($perPage = 25)
    {
        return $this->repository->active($perPage);
    }
}
