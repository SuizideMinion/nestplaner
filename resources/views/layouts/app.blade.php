<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'NestPlaner') }}</title>

    {{-- Bootstrap + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: #f8fafc;
        }
        .navbar-brand {
            font-weight: 600;
            color: #2c3e50 !important;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-house-heart-fill text-success"></i> NestPlaner
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                @auth
                    {{-- Familien-Auswahl --}}
                    @if(Auth::user()->families->count() > 0)
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-people-fill text-primary"></i>
                                {{ optional(Auth::user()->families->firstWhere('id', session('active_family_id')))->name ?? 'Familie wählen' }}
                            </a>
                            <ul class="dropdown-menu">
                                @foreach(Auth::user()->families as $family)
                                    <li>
                                        <a class="dropdown-item {{ session('active_family_id') == $family->id ? 'active' : '' }}"
                                           href="{{ route('family.switch', $family->id) }}">
                                            {{ $family->name }}
                                            @if(session('active_family_id') == $family->id)
                                                <i class="bi bi-check2 text-success"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-success" href="{{ route('family.create') }}">
                                        <i class="bi bi-plus-circle"></i> Neue Familie erstellen
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-secondary" href="{{ route('family.index') }}">
                                        <i class="bi bi-list-ul"></i> Familienübersicht
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    {{-- Benutzer Dropdown --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('calendar.index') }}"><i class="bi bi-calendar-event"></i> Kalender</a></li>
                            <li><a class="dropdown-item" href="{{ route('family.index') }}"><i class="bi bi-people"></i> Familien</a></li>
                            <li><a class="dropdown-item" href="{{ route('recurring.index') }}"><i class="bi bi-people"></i> Events</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Registrieren</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- HAUPTINHALT --}}
<main class="flex-grow-1 py-4">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{ $slot ?? '' }}
    </div>
</main>

{{-- FOOTER --}}
<footer class="bg-white text-center py-3 mt-auto border-top small text-muted">
    &copy; {{ date('Y') }} NestPlaner — Gemeinsam planen. Gemeinsam leben.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
