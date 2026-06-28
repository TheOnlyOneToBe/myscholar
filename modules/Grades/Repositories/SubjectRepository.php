<?php

namespace Modules\Grades\Repositories;

use Modules\Grades\Models\Subject;

class SubjectRepository
{
    public function all($perPage = 25)
    {
        return Subject::paginate($perPage);
    }

    public function active($perPage = 25)
    {
        return Subject::active()->paginate($perPage);
    }

    public function findById($id)
    {
        return Subject::findOrFail($id);
    }

    public function findByCode($code)
    {
        return Subject::where('code', $code)->firstOrFail();
    }

    public function create(array $data): Subject
    {
        return Subject::create($data);
    }

    public function update($id, array $data): bool
    {
        return Subject::findOrFail($id)->update($data);
    }

    public function delete($id): bool
    {
        return Subject::findOrFail($id)->delete();
    }
}
