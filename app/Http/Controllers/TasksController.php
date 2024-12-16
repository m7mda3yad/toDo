<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Repositories\TaskRepository;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class TasksController extends Controller
{
    protected $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $tasks = $this->repository->paginate(10);
        return response()->json($tasks);
    }

    // Create a new task (API)
    public function store(TaskRequest $request)
    {
        $task = $this->repository->create($request->all());
        return response()->json(['message' => 'Task created.', 'task' => $task], 201);
    }

    public function create()
{
    $categories = \App\Models\Category::all();  // Fetch all categories
    return response()->json([
        'categories' => $categories
    ]);
}
    // Show a specific task (API)
    public function show($id)
    {
        $task = $this->repository->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        return response()->json(['task' => $task], 200);
    }

    // Edit a specific task (API)
    public function edit($id)
    {
        $task = $this->repository->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        return response()->json(['task' => $task], 200);
    }

    // Update a task (API)
    public function update(TaskRequest $request, $id)
    {
        $task = $this->repository->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        $updatedTask = $this->repository->update($request->all(), $id);
        return response()->json(['message' => 'Task updated.', 'task' => $updatedTask], 200);
    }

    // Soft delete a task (API)
    public function destroy($id)
    {
        $task = $this->repository->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        $task->delete();  // Soft delete the task
        return response()->json(['message' => 'Task deleted.'], 200);
    }

    // Restore a soft-deleted task (API)
    public function restore($id)
    {
        $task = $this->repository->withTrashed()->find($id);  // Get the task, even if it's soft-deleted

        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        $task->restore();  // Restore the task
        return response()->json(['message' => 'Task restored.', 'task' => $task], 200);
    }
}
