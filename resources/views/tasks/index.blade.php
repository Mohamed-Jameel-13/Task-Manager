@extends('layouts.app')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1><i class="fas fa-clipboard-list me-2"></i>My Tasks</h1>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Task
        </a>
    </div>
</div>

<div class="row">
    @forelse($tasks as $task)
        <div class="col-md-4">
            <div class="card task-{{ str_replace('_', '-', $task->status) }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $task->title }}</h5>
                    <span class="badge rounded-pill badge-{{ str_replace('_', '-', $task->status) }}">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        {{ Str::limit($task->description, 100) ?? 'No description provided' }}
                    </p>
                    <div class="text-muted small mb-3">
                        <i class="far fa-clock me-1"></i> Created {{ $task->created_at->diffForHumans() }}
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i> View
                        </a>
                        <div>
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning me-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-tasks fa-3x mb-3 text-muted"></i>
                    <h3>No Tasks Found</h3>
                    <p class="text-muted mb-3">You don't have any tasks yet. Create your first task to get started!</p>
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create First Task
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection