<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    // Fetch all tasks
    public function taskList()
    {
        $tasks = Task::paginate(5); // Paginate tasks, 5 per page
        return response()->json($tasks);
    }

    // Add a new task
    public function addTask(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:completed,pending',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx',
        ]);

        $task = new Task();
        $task->title = $request->title;
        $task->status = $request->status;

        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('attachments');
            $task->attachment = $filePath;
        }

        $task->save();
        return response()->json($task, 201);
    }
}
