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
        if($request->search)
        $tasks = auth()->user()->tasks()->with('category')->
            where('title','like',"%$request->search%")->
            OrWhere('description','like',"%$request->search%")->paginate(10);

        else
            $tasks = auth()->user()->tasks()->with('category')->paginate(10);

        return response()->json($tasks);
    }

    public function store(TaskRequest $request)
    {
        $task = $this->repository->create($request->all());
        return response()->json(['message' => 'Task created.', 'task' => $task], 201);
    }

    public function create()
{
    $categories = \App\Models\Category::all();
    return response()->json([
        'categories' => $categories
    ]);
}
    public function edit($id)
    {
        $task = $this->repository->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        return response()->json(['task' => $task], 200);
    }

    public function update(TaskRequest $request, $id)
    {
        $task = $this->repository->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }

        $updatedTask = $this->repository->update($request->all(), $id);
        return response()->json(['message' => 'Task updated.', 'task' => $updatedTask], 200);
    }

    public function destroy($id)
    {
        $task = $this->repository->find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted.'], 200);
    }

    public function restore($id)
    {
        $task = $this->repository->withTrashed()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found.'], 404);
        }
        $task->restore();
        return response()->json(['message' => 'Task restored.', 'task' => $task], 200);
    }
}
