<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private $tasks;

    // public function __construct()
    // {

    // }

    public function index()
    {
        $pageTitle = 'Task List'; // Ditambahkan
        $tasks = Task::all(); // Diperbarui
        return view('tasks.index', [
            'pageTitle' => $pageTitle, //Ditambahkan
            'tasks' => $tasks,
        ]);
    }
    public function create()
    {
        $pageTitle = 'Create Task';
        return view('tasks.create', ['pageTitle' => $pageTitle]);
    }


    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'detail' => 'required',
                'due_date' => 'required',
                'status' => 'required',
            ],
            $request->all()
        );

        $task = new Task([
            'name' => $request->name,
            'detail' => $request->detail,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);

        $task->save();

        return redirect()->route('tasks.index');
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Task';
        $task = Task::find($id);
        return view('tasks.edit', ['pageTitle' => $pageTitle, 'task' => $task]);
    }


    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'name' => 'required',
            'detail' => 'required',
            'due_date' => 'required|date',
            'status' => 'required|in:not_started,in_progress,in_review,completed',
        ]);

        $task = Task::find($id);
        $task->name = $request->input('name');
        $task->detail = $request->input('detail');
        $task->due_date = $request->input('due_date');
        $task->status = $request->input('status');
        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }


    public function delete($id)
    {
        $task = Task::find($id);
        $task->delete();
        return redirect()->route('tasks.index');
    }

    public function progress()
    {
        $pageTitle = 'Task Progres';
        $tasks = Task::all();
        $filteredTasks = $tasks->groupBy('status');
        //echo $filteredTasks;

        $tasks = [
            Task::STATUS_NOT_STARTED => $filteredTasks->get(
                Task::STATUS_NOT_STARTED, []
            ),
            Task::STATUS_IN_PROGRESS => $filteredTasks->get(
                Task::STATUS_IN_PROGRESS, []
            ),
            Task::STATUS_IN_REVIEW => $filteredTasks->get(
                Task::STATUS_IN_REVIEW, []
            ),
            Task::STATUS_COMPLETED => $filteredTasks->get(
                Task::STATUS_COMPLETED, []
            ),
        ];

        //dump($tasks);

        return view('tasks.progress', [
            'pageTitle' => $pageTitle,
            'tasks' => $tasks,
        ]);
    }
}
