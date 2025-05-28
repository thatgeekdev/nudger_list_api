<?php

namespace App\Http\Controllers\API;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use Sanctum token",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="sanctum"
 * )
 */
class TaskController extends Controller
{
    use AuthorizesRequests;
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="List all tasks",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Successful response")
     * )
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $request->user()->tasks()->latest()->get();

        return response($tasks, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/search/tasks/{query}",
     *     tags={"Tasks"},
     *     summary="Search tasks by title or description",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="query", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Search results")
     * )
     */
    public function search(string $query)
    {
        $this->authorize('search', Task::class);

        $tasks = Task::where('user_id', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Tasks retrieved successfully',
            'data' => $tasks,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Create a new task",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", example="Study Laravel"),
     *             @OA\Property(property="description", type="string", example="Complete the policy module")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Task created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
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
     * @OA\Get(
     *     path="/api/tasks/{task}",
     *     tags={"Tasks"},
     *     summary="Show a single task",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="task", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Task details"),
     *     @OA\Response(response=404, description="Task not found")
     * )
     */
    public function show(Request $request, Task $task): Response
    {
        $this->authorize('view', $task);

        return response($task, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{task}",
     *     tags={"Tasks"},
     *     summary="Update a task",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="task", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="status", type="string", enum={"pending","in_progress","completed"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Task updated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
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
     * @OA\Delete(
     *     path="/api/tasks/{task}",
     *     tags={"Tasks"},
     *     summary="Soft delete a task",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="task", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Task deleted")
     * )
     */
    public function destroy(Request $request, Task $task): Response
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response(['message' => 'Task deleted'], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/tasks/status/{status}",
     *     tags={"Tasks"},
     *     summary="Filter tasks by status",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="status", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Filtered tasks")
     * )
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
     * @OA\Patch(
     *     path="/api/tasks/{task}/status",
     *     tags={"Tasks"},
     *     summary="Update task status",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="task", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"pending","in_progress","completed"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated")
     * )
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
     * @OA\Patch(
     *     path="/api/tasks/{id}/restore",
     *     tags={"Tasks"},
     *     summary="Restore a soft-deleted task",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Task restored")
     * )
     */
    public function restore(Request $request, int $id): Response
    {
        $task = Task::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $task);

        $task->restore();

        return response(['message' => 'Task restored', 'task' => $task], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/trashed/tasks",
     *     tags={"Tasks"},
     *     summary="List soft-deleted tasks",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="List of trashed tasks")
     * )
     */

    public function trashed(Request $request): Response
    {
        $this->authorize('viewTrashed', Task::class);

        $tasks = $request->user()->tasks()->onlyTrashed()->get();

        return response($tasks, 200);
    }
    /**
     * @OA\Get(
     *     path="/api/task/trashed/{id}",
     *     tags={"Tasks"},
     *     summary="Show a single trashed task",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Trashed task details"),
     *     @OA\Response(response=404, description="Task not found")
     * )
     */

    public function showTrashed(Request $request, int $id): Response
    {
        $task = Task::onlyTrashed()->where('user_id', $request->user()->id)->findOrFail($id);

        $this->authorize('showTrashed', $task);

        return response($task, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/tasks/{id}/force",
     *     tags={"Tasks"},
     *     summary="Force delete a soft-deleted task",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Task permanently deleted")
     * )
     */
    public function forceDelete(Request $request, int $id): Response
    {
        $task = Task::onlyTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $task);

        $task->forceDelete();

        return response(['message' => 'Task permanently deleted'], 200);
    }
}
