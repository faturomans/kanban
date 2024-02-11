<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private $tasks;

    // public function __construct()
    // {

    // }
    public function index()
    {
        $pageTitle = 'Task List';
        $tasks = Task::all();
        return view('tasks.index', [
            'pageTitle' => $pageTitle,
            'tasks' => $tasks,
        ]);
    }

    public function create( Request $request)
    {
        $task = new Task();
        $id = $request->input('id');
        $status = $request->input('status');
        $task = Task::find($id);
        $pageTitle = "Create Task";
        return view('tasks.create', compact('pageTitle', 'task' , 'status'));
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Task';
        $task = Task::find($id);
        return view('tasks.edit', ['pageTitle' => $pageTitle, 'task' => $task]);
        $pageTitle = 'Task List';
        $tasks = Task::all();
        return view('tasks.index', [
            'pageTitle' => $pageTitle,
            'tasks' => $tasks,
        ]);
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
            'user_id' => Auth::user()->id, // Ditambahkan
        ]);

        $task->save();

        return redirect()->route('tasks.index');
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
        $pageTitle = "Delete Task";
        return view('tasks.delete', compact('pageTitle', 'task'));
    }
    public function destroy($id)
    {
        $task = Task::find($id);
        $task->delete();
        return redirect()->route('tasks.index');
    }

    public function progress()
    {
        $title = 'Taks Progress';
        $allTasks = Task::all();
        $filteredTasks = $allTasks->groupBy('status');
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
        return view('tasks.progress', [
            'pageTitle' => $title,
            'tasks' => $tasks,
        ]);
    }

    public function move(int $id, Request $request)
    {
        $task = Task::findOrFail($id);

        $task->update([
            'status' => $request->status,
        ]);

        return redirect()->route('tasks.progress');
    }

    public function complete($id)
    {
        $task = Task::findOrFail($id);
        $task->status = 'completed';
        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Task marked as completed successfully.');
    }

    public function completee($id)
    {
        $task = Task::findOrFail($id);
        $task->status = 'completed';
        $task->save();

        return redirect()->route('tasks.progress')->with('success', 'Task marked as completed successfully.');
    }

    public function home()
    {
        $tasks = Task::where('user_id', auth()->id())->get();

        $completed_count = $tasks
            ->where('status', Task::STATUS_COMPLETED)
            ->count();

        $uncompleted_count = $tasks
            ->whereNotIn('status', Task::STATUS_COMPLETED)
            ->count();

        return view('home', [
            'completed_count' => $completed_count,
            'uncompleted_count' => $uncompleted_count,
        ]);
    }



}
