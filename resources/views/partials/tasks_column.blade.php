<div class="task-progress-column">
    <div class="task-progress-column-heading">
    @foreach ($tasks as $task)
      <h2>{{ $title }}</h2>
      <a href="{{ $task->status ? route('tasks.create', ['id' => $task->id, 'status' => $task->status]) : '#' }}" class="material-icons task-progress-card-top-edit">add</a>
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
