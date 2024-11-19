<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    // Fetch all tasks
    public function taskList()
    {
        $tasks = Task::paginate(5); // Paginate tasks, 5 per page
        return response()->json([
            'response' => [
                'code' => 200,
                'data' => $tasks,
            ]
        ]);
    }


    // Add a new task
    public function addTask(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'status' => 'required|in:completed,pending',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx',
            ]);

            $task = new Task();
            $task->title = $request->title;
            $task->status = $request->status;

            if ($request->hasFile('attachment')) {
                $filePath = $request->file('attachment')->store('attachments', 'public');
                $task->attachment = asset('storage/' . $filePath);
            }

            $task->save();

            return response()->json([
                'response' => [
                    'code' => 200,
                    'message' => 'Task created successfully',
                    'data' => $task,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::info($e->getMessage());
            return response()->json(['response' => ['code' => 422, 'message' => $e->getMessage(), 'errors' => $e->errors()]], 422);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['response' => ['code' => 500, 'message' => 'An error occurred', 'error' => $e->getMessage()]], 500);
        }
    }


    // Update an existing task
    public function updateTask(Request $request)
    {
        try {
            $id = $request->id;
            $task = Task::find($id);

            if (!$task) {
                return response()->json(['response' => ['code' => 404, 'message' => 'Task not found']], 404);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'status' => 'required|in:completed,pending',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx',
            ]);

            $task->update($request->only(['title', 'status']));

            if ($request->hasFile('attachment')) {
                if ($task->attachment) {
                    Storage::delete($task->attachment);
                }

                $filePath = $request->file('attachment')->store('attachments', 'public');
                $task->attachment = asset('storage/' . $filePath);
            }

            $task->save();

            return response()->json([
                'response' => [
                    'code' => 200,
                    'message' => 'Task updated successfully',
                    'data' => $task,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::info($e->getMessage());
            return response()->json(['response' => ['code' => 422, 'message' => $e->getMessage(), 'errors' => $e->errors()]], 422);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['response' => ['code' => 500, 'message' => 'An error occurred', 'error' => $e->getMessage()]], 500);
        }
    }


    // Delete a task
    public function deleteTask(Request $request)
    {
        try {
            $id = $request->id;

            $task = Task::find($id);

            if (!$task) {
                return response()->json(['response'=>['code'=> 404,'message'=> 'Task not found']]);
            }

            if ($task->attachment) {
                Storage::delete($task->attachment);
            }

            $task->delete();

            return response()->json(['response'=>['code'=> 200,'message'=> 'Task deleted successfully']]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['response' => ['code' => 500, 'message' => 'An error occurred', 'error' => $e->getMessage()]], 500);
        }
    }

}
