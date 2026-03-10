<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'DentControl')</title>
    <link rel="stylesheet" href="{{ asset('css/stylesDashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="logo">
            <img src="{{ asset('images/logooo.png') }}" alt="DentControl">
            </div>

            <nav class="menu">
    <a href="/{{ auth()->user()->rol }}/dashboard" class="{{ Request::is('*/dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>

    @if(auth()->user()->rol === 'dentista')
        <a href="#">
            <i class="fa-solid fa-calendar-day"></i> Agenda del día
        </a>
        <a href="#">
            <i class="fa-solid fa-notes-medical"></i> Tratamientos
        </a>
        <a href="#">
            <i class="fa-solid fa-folder-open"></i> Historial clínico
        </a>
        <a href="#">
            <i class="fa-solid fa-chart-pie"></i> Reportes
        </a>
        <a href="{{ route('catalogos.index') }}" class="{{ Request::is('catalogos*') ? 'active' : '' }}">
            <i class="fa-solid fa-list-check"></i> Registrar catálogos
        </a>
    @endif

    @if(auth()->user()->rol === 'asistente')
        <a href="#">
            <i class="fa-solid fa-calendar-day"></i> Agenda del día
        </a>
        <a href="{{ route('pacientes.index') }}">
            <i class="fa-solid fa-user"></i> Pacientes
        </a>
        <a href="#">
            <i class="fa-solid fa-folder-open"></i> Historial clínico
        </a>
        <a href="#">
            <i class="fa-solid fa-mobile-screen-button"></i> Acceso App Móvil
        </a>
        <a href="#">
            <i class="fa-solid fa-gears"></i> Gestión de App
        </a>
    @endif
</nav>

            <div class="logout">
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
      </form>
      <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: inherit; text-decoration: none;">
          <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
      </a>
    </div>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>
</body>
</html>