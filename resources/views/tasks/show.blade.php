@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card task-{{ str_replace('_', '-', $task->status) }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $task->title }}</h4>
                <span class="badge rounded-pill badge-{{ str_replace('_', '-', $task->status) }}">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5 class="card-title">Description</h5>
                    <p class="card-text">{{ $task->description ?? 'No description provided' }}</p>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <strong><i class="far fa-calendar-plus me-1"></i> Created:</strong>
                            {{ $task->created_at->format('M d, Y h:i A') }}
                        </div>
                        <div>
                            <strong><i class="far fa-calendar-check me-1"></i> Last Updated:</strong>
                            {{ $task->updated_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Tasks
                    </a>
                    <div>
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection