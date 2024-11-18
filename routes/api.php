<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('tasks', [TaskController::class, 'taskList']);
Route::post('addTask', [TaskController::class, 'addTask'])->name('add-task');
Route::post('editTask/{id}', [TaskController::class, 'updateTask'])->name('update-task');
Route::post('deleteTask/{id}',[TaskController::class, 'deleteTask'])->name('delete-task');
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
