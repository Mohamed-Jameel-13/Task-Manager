<form method="POST" action="{{ $action }}">
    @csrf
    @if($method != 'POST')
        @method($method)
    @endif
    
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $task->title ?? '') }}" required>
    </div>
    
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $task->description ?? '') }}</textarea>
    </div>
    
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select" id="status" name="status">
            <option value="pending" {{ (old('status', $task->status ?? '') == 'pending') ? 'selected' : '' }}>Pending</option>
            <option value="in_progress" {{ (old('status', $task->status ?? '') == 'in_progress') ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ (old('status', $task->status ?? '') == 'completed') ? 'selected' : '' }}>Completed</option>
        </select>
    </div>
    
    <button type="submit" class="btn btn-primary">{{ $submitText }}</button>
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
</form>
