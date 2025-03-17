<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #343a40 !important;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        .task-pending {
            border-left: 5px solid #ffc107;
        }
        .task-in-progress {
            border-left: 5px solid #17a2b8;
        }
        .task-completed {
            border-left: 5px solid #28a745;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-in-progress {
            background-color: #17a2b8;
            color: white;
        }
        .badge-completed {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('tasks.index') }}">
                <i class="fas fa-tasks me-2"></i>Task Manager
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tasks.index') }}">
                            <i class="fas fa-list me-1"></i> All Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tasks.create') }}">
                            <i class="fas fa-plus me-1"></i> New Task
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">© {{ date('Y') }} Task Manager. All rights reserved.</p>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 for nice alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Global script for delete confirmations
        $(document).ready(function() {
            $('.delete-form').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.unbind('submit').submit();
                    }
                });
            });
            
            // Form validation for task form
            $('.task-form').on('submit', function(e) {
                const titleField = $('#title');
                
                if (titleField.val().trim() === '') {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Error!',
                        text: 'Task title cannot be empty',
                        icon: 'error',
                        confirmButtonColor: '#007bff'
                    });
                    
                    titleField.addClass('is-invalid');
                    return false;
                }
                
                titleField.removeClass('is-invalid');
            });
            
            // Live validation
            $('#title').on('input', function() {
                const titleField = $(this);
                if (titleField.val().trim() === '') {
                    titleField.addClass('is-invalid');
                    $('#title-feedback').text('Task title is required');
                } else if (titleField.val().trim().length < 3) {
                    titleField.addClass('is-invalid');
                    $('#title-feedback').text('Task title must be at least 3 characters');
                } else {
                    titleField.removeClass('is-invalid').addClass('is-valid');
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>