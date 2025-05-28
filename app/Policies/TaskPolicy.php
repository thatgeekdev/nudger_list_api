<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine if the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view a specific task.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine if the user can create tasks.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update a task.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine if the user can update the status of a task.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine if the user can filter tasks by status.
     */
    public function filterByStatus(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can soft delete a task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine if the user can view trashed tasks.
     */
    public function viewTrashed(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view a specific trashed task.
     */
    public function showTrashed(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine if the user can restore a trashed task.
     */
    public function restore(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine if the user can force delete a task.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }
    /**
     * Determine if the user can search tasks.
     */
    public function search(User $user): bool
    {
        return true;
    }
}
