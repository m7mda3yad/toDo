<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasksController;

Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TasksController::class);
    Route::patch('tasks/{id}/restore', [TasksController::class, 'restore']);
});
