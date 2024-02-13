<div class="task-progress-card">
    <div class="task-progress-card-left">
    @if ($task->status == 'completed')
        <div class="material-icons task-progress-card-top-checked" onclick="submitForm('{{ route('tasks.completee', ['id' => $task->id]) }}')">check_circle</div>
    @else
        <div class="material-icons check-icon" onclick="submitForm('{{ route('tasks.completee', ['id' => $task->id]) }}')">check_circle</div>
    @endif
        <a href="{{ route('tasks.edit', ['id' => $task->id]) }}" class="material-icons task-progress-card-top-edit">more_vert</a>
    </div>
    <p class="task-progress-card-title">{{ $task->name }}</p>
    <div>
        <p>{{ $task->detail }}</p>
    </div>
    <div>
        <p>Due on {{ $task->due_date }}</p>
    </div>
    <div>
        <p>Owner: <strong>{{ $task->user->name }}</strong></p>
      </div>
    <div class="@if ($leftStatus) task-progress-card-left @else task-progress-card-right @endif">
        @if ($leftStatus)
            <form action="{{ route('tasks.move', ['id' => $task->id, 'status' => $leftStatus]) }}" method="POST">
            @method('patch')
            @csrf
            <button class="material-icons">chevron_left</button>
            </form>
         @endif

        @if ($rightStatus)
            <form action="{{ route('tasks.move', ['id' => $task->id, 'status' => $rightStatus]) }}"
            method="POST">
            @method('patch')
            @csrf
            <button class="material-icons">chevron_right</button>
            </form>
        @endif
    </div>
</div>

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
