<div class="task-progress-column">
    <div class="task-progress-column-heading">
    @foreach ($tasks as $task)
        <div class="container">
            <h2>{{ $title }}</h2> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            <a href="{{ $task->status ? route('tasks.create', ['id' => $task->id, 'status' => $task->status]) : '#' }}" class="material-icons task-progress-card-top-editt">add</a>
        </div>
    </div>
    <div>
        @include('partials.task_card', [
          'task' => $task,
          'leftStatus' => $leftStatus,
          'rightStatus' => $rightStatus,
        ])
      @endforeach
    </div>
</div>

<style>
    .container {
        display: flex;

    }
    .material-icons.task-progress-card-top-editt {
        margin-right: 5px;
        margin-top: 25px;
        text-decoration: none;
    }
    .container a {
        margin: 0 5px;
    }
</style>
