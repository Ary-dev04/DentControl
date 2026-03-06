<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Dashboard SaaS | DentControl')</title>

  <link rel="stylesheet" href="{{ asset('css/stylesBaseSaas.css') }}">
  <link rel="stylesheet" href="{{ asset('css/saas-dashboard.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<div class="app-container">

  <aside class="sidebar">
    <div class="logo">
      <img src="{{ asset('images/logooo.png') }}" alt="DentControl">
    </div>

    <nav class="menu">
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-house"></i> Dashboard
      </a>

      <a href="{{ route('clinicas.index') }}">
        <i class="fa-solid fa-hospital"></i> Registrar clínica
      </a>

      <a href="{{ route('usuarios.index') }}">
        <i class="fa-solid fa-users"></i> Registrar usuarios
      </a>

      <a href="#">
        <i class="fa-solid fa-chart-column"></i> Reportes
      </a>
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
@stack('scripts')
</body>
</html>