<?php

namespace Modules\Classes\Policies;

use Modules\Auth\Models\User;
use Modules\Classes\Models\ClassModel;

class ClassPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ClassModel $class): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ClassModel $class): bool
    {
        return true;
    }

    public function delete(User $user, ClassModel $class): bool
    {
        return true;
    }

    public function manageAssignments(User $user, ClassModel $class): bool
    {
        return true;
    }
}
