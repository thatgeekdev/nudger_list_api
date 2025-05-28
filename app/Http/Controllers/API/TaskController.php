<?php

namespace App\Http\Controllers\API;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the user's tasks.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $request->user()->tasks()->latest()->get();

        return response($tasks, 200);
    }
    public function search(string $query)
    {
        $this->authorize('search', Task::class);

        $tasks = Task::where('user_id', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
            })
            ->get();

        return response()->json($tasks);
    }
    /**
     * Store a newly created task.
     */
    public function store(Request $request): Response
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = $request->user()->tasks()->create($validated);

        return response($task, 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Request $request, Task $task): Response
    {
        $this->authorize('view', $task);

        return response($task, 200);
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task): Response
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
        ]);

        $task->update($validated);

        return response($task, 200);
    }

    /**
     * Soft delete the specified task.
     */
    public function destroy(Request $request, Task $task): Response
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response(['message' => 'Task deleted'], 200);
    }

    /**
     * Filter tasks by status.
     */
    public function filterByStatus(Request $request, string $status): Response
    {
        $this->authorize('filterByStatus', Task::class);

        $allowedStatuses = ['pending', 'in_progress', 'completed'];

        if (!in_array($status, $allowedStatuses)) {
            return response(['error' => 'Invalid status'], 422);
        }

        $tasks = $request->user()->tasks()->where('status', $status)->get();

        return response($tasks, 200);
    }

    /**
     * Update the status of a task.
     */
    public function updateStatus(Request $request, Task $task): Response
    {
        $this->authorize('updateStatus', $task);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update(['status' => $validated['status']]);

        return response($task, 200);
    }

    /**
     * Restore a soft-deleted task.
     */
    public function restore(Request $request, int $id): Response
    {
        $task = Task::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $task);

        $task->restore();

        return response(['message' => 'Task restored', 'task' => $task], 200);
    }

    public function trashed(Request $request): Response
    {
        $this->authorize('viewTrashed', Task::class);

        $tasks = $request->user()->tasks()->onlyTrashed()->get();

        return response($tasks, 200);
    }

    public function showTrashed(Request $request, int $id): Response
    {
        $task = Task::onlyTrashed()->where('user_id', $request->user()->id)->findOrFail($id);

        $this->authorize('showTrashed', $task);

        return response($task, 200);
    }

    /**
     * Permanently delete a soft-deleted task.
     */
    public function forceDelete(Request $request, int $id): Response
    {
        $task = Task::onlyTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $task);

        $task->forceDelete();

        return response(['message' => 'Task permanently deleted'], 200);
    }
    
}
