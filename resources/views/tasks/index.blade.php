@extends('layouts.master')
@section('pageTitle', $pageTitle)
@section('pageTitle', 'Home')
@section('main')
  <div class="task-list-container">
    <h1 class="task-list-heading">Task List</h1>
    <!-- Paste the code below -->
    <div class="task-list-task-buttons">
        <a href="{{ route('tasks.create') }}">
        <button  class="task-list-button">
            <span class="material-icons">add</span>Add task
        </button>
        </a>
    </div>
    <div class="task-list-table-head">
        <div class="task-list-header-task-name">Task Name</div>
        <div class="task-list-header-detail">Detail</div>
        <div class="task-list-header-due-date">Due Date</div>
        <div class="task-list-header-progress">Progress</div>
        <div class="task-list-header-progress">File</div>
        <div class="task-list-header-owner-name">Owner</div>
    </div>

    @foreach ($tasks as $index => $task)
      <div class="table-body">
        <div class="table-body-task-name">
            @if ($task->status == 'completed')
            <div class="material-icons task-progress-card-top-checked" onclick="submitForm('{{ route('tasks.complete', ['id' => $task->id]) }}')">check_circle</div>
        @else
            <div class="material-icons check-icon" onclick="submitForm('{{ route('tasks.complete', ['id' => $task->id]) }}')">check_circle</div>
        @endif
          {{ $task->name }}
        </div>
        <div class="table-body-detail"> {{ $task->detail }} </div>
        <div class="table-body-due-date"> {{ $task->due_date }} </div>
        <div class="table-body-progress">
          @switch($task->status)
            @case('in_progress')
              In Progress
              @break
            @case('in_review')
              Waiting/In Review
              @break
            @case('completed')
              Completed
              @break
            @default
              Not Started
          @endswitch
            </div>
             <!-- Tambahkan code di bawah -->
        <div class="table-body-file">
            @foreach ($task->files as $file)
              <a href="{{ route('tasks.files.show', ['task_id' => $task->id, 'id' => $file->id]) }}">
                {{ $file->filename }}</a>
            @endforeach
          </div>
          <!-- Sampai di sini -->

            <div class="table-body-owner-name">{{ $task->user->name }}</div>
            <!-- Ditambahkan -->
            <div class="table-body-links">
                @can('update', $task)
                  <a href="{{ route('tasks.edit', ['id' => $task->id]) }}">Edit</a>
                @endcan
                @can('delete', $task)
                  <a href="{{ route('tasks.delete', ['id' => $task->id]) }}">Delete</a>
                @endcan
              </div>
        </div>

    @endforeach
  </div>
  @endsection
<style>
    .task-list-header-owner-name {
        padding: 16px;
        width: 15%;
    }
    .table-body-progress {
        width: 15%;
        padding: 16px;
        border-right: 1px solid #d8d8d8; /* Ditambahkan */
    }

        .table-body-owner-name {
        width: 15%;
        padding: 16px;
}
    a {
        text-decoration: none;
    }
</style>
  <script>
    function submitForm(route) {
        var form = document.createElement('form');
        form.setAttribute('method', 'POST');
        form.setAttribute('action', route);

        var hiddenField = document.createElement('input');
        hiddenField.setAttribute('type', 'hidden');
        hiddenField.setAttribute('name', '_method');
        hiddenField.setAttribute('value', 'PATCH');

        var csrfField = document.createElement('input');
        csrfField.setAttribute('type', 'hidden');
        csrfField.setAttribute('name', '_token');
        csrfField.setAttribute('value', '{{ csrf_token() }}');

        form.appendChild(hiddenField);
        form.appendChild(csrfField);

        document.body.appendChild(form);
        form.submit();
    }
</script>
