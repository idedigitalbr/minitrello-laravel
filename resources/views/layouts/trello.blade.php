<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mini Trello') }} - @yield('title', 'Dashboard')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --trello-blue: #0079bf;
            --trello-blue-dark: #026aa7;
            --trello-green: #61bd4f;
            --trello-yellow: #f2d600;
            --trello-orange: #ff9f1a;
            --trello-red: #eb5a46;
            --trello-purple: #c377e0;
            --trello-pink: #ff78cb;
            --trello-sky: #00c2e0;
            --trello-lime: #51e898;
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .board-container {
            min-height: calc(100vh - 76px);
            overflow-x: auto;
        }

        .board-lists {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            min-height: 100%;
            align-items: flex-start;
        }

        .task-list {
            background: #ebecf0;
            border-radius: 12px;
            min-width: 280px;
            max-width: 280px;
            max-height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
        }

        .task-list-header {
            padding: 0.75rem 1rem;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .task-list-cards {
            padding: 0 0.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .card-item {
            background: white;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
            cursor: pointer;
            transition: all 0.2s;
        }

        .card-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
        }

        .card-item.dragging {
            opacity: 0.5;
            transform: rotate(3deg);
        }

        .add-card-form,
        .add-list-form {
            padding: 0.5rem;
        }

        .board-card {
            border-radius: 12px;
            min-height: 120px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .board-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .color-option {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
        }

        .color-option:hover,
        .color-option.selected {
            transform: scale(1.1);
            border-color: #333;
        }

        .due-date-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .due-date-overdue {
            background: var(--trello-red);
            color: white;
        }

        .due-date-soon {
            background: var(--trello-yellow);
            color: #333;
        }

        .due-date-normal {
            background: #dfe1e6;
            color: #333;
        }

        .sortable-ghost {
            opacity: 0.4;
            background: #c4c9cc;
        }

        .add-list-btn {
            min-width: 280px;
            background: rgba(255, 255, 255, 0.24);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }

        .add-list-btn:hover {
            background: rgba(255, 255, 255, 0.32);
        }

        @yield('styles')
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(0,0,0,0.15);">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-kanban me-2"></i>Mini Trello
            </a>

            <div class="d-flex align-items-center gap-3">
                @auth
                    <span class="text-white-50">{{ Auth::user()->name }}</span>
                    <div class="dropdown">
                        <button class="btn btn-link text-white p-0" data-bs-toggle="dropdown">
                            <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                        class="bi bi-person me-2"></i>Perfil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Sair
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Content -->
    @yield('content')

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SortableJS for drag and drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        // CSRF Token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Helper function for fetch requests
        async function fetchApi(url, method = 'GET', data = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            };
            if (data) options.body = JSON.stringify(data);
            const response = await fetch(url, options);
            return response.json();
        }
    </script>

    @yield('scripts')
</body>

</html>