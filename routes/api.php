<?php
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\API\TaskController;

Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    return response()->json($user, 201);
});


Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return response()->json([
        'token' => $user->createToken('api-token')->plainTextToken,
    ]);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']); 

    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::get('/tasks/status/{status}', [TaskController::class, 'filterByStatus']);
    
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']); //soft delete just set deleted_at

    Route::get('/trashed/tasks', [TaskController::class, 'trashed']); //list trashed tasks (... just switched the route text position to avoid route conflicts)
    Route::get('/task/trashed/{id}', [TaskController::class, 'showTrashed']);

    Route::patch('/tasks/{id}/restore', [TaskController::class, 'restore']); // only restore deleted task, not when delete was already forced with the next route
    Route::delete('/tasks/{id}/force', [TaskController::class, 'forceDelete']); // depends on the soft delete, delete the task permanently

    Route::get('/search/tasks/{query}', [TaskController::class, 'search']);


    Route::get('/user/{id}', function (User $id) {
        $user = User::find($id->id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user, 200);
    });

});