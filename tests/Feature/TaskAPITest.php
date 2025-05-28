<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task'
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Test Task']);
    }

    public function test_user_can_list_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks');

        $response->assertStatus(200)->assertJsonCount(2);
    }

    public function test_user_can_view_single_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)->assertJsonFragment(['id' => $task->id]);
    }

    public function test_user_can_update_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'status' => 'in_progress'
        ]);

        $response->assertStatus(200)->assertJsonFragment(['title' => 'Updated Task']);
    }

    public function test_user_can_delete_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
    }

    public function test_user_can_filter_by_status(): void
    {
        $user = User::factory()->create();
        Task::factory()->create(['user_id' => $user->id, 'status' => 'completed']);
        Task::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks/status/completed');

        $response->assertStatus(200)->assertJsonCount(1);
    }

    public function test_user_can_restore_soft_deleted_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $task->delete();

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/tasks/{$task->id}/restore");

        $response->assertStatus(200)->assertJsonFragment(['message' => 'Task restored']);
    }

    public function test_user_can_force_delete_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $task->delete();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/tasks/{$task->id}/force");

        $response->assertStatus(200)->assertJsonFragment(['message' => 'Task permanently deleted']);
    }
    public function test_user_can_update_task_status(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'completed'
        ]);

        $response->assertStatus(200)->assertJsonFragment(['status' => 'completed']);
    }

    public function test_user_can_list_trashed_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(2)->create(['user_id' => $user->id]);
        $trashedTask = Task::factory()->create(['user_id' => $user->id]);
        $trashedTask->delete();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/trashed/tasks');

        $response->assertStatus(200)->assertJsonFragment(['id' => $trashedTask->id]);
    }

    public function test_user_can_view_trashed_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $task->delete();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/task/trashed/{$task->id}");

        $response->assertStatus(200)->assertJsonFragment(['id' => $task->id]);
    }

    public function test_user_can_search_tasks(): void
    {
        $user = User::factory()->create();

        Task::factory()->create(['user_id' => $user->id, 'title' => 'Finish Laravel Tests']);
        Task::factory()->create(['user_id' => $user->id, 'title' => 'Write Documentation']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/search/tasks/laravel');

        $response->assertStatus(200)->assertJsonFragment(['title' => 'Finish Laravel Tests']);
    }
}
