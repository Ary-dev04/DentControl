<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | DentControl</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/stylesLogin.css') }}">
</head>

<body class="login-body">

<div class="login-container">

    <!-- LADO IZQUIERDO -->
    <div class="login-left">
        <img src="{{ asset('images/logooo.png') }}" alt="DentControl">
        <h1>DentControl</h1>
        <p>Gestión clínica dental segura y profesional</p>
    </div>

    <!-- LADO DERECHO -->
    <div class="login-right">
        <div class="login-card">

            <form class="login-form" method="POST" action="{{ route('login.post') }}">
                @csrf

                <h2>Iniciar sesión</h2>

                @if ($errors->any())
                    <div class="error-message" style="color: #ef4444; font-size: 0.875rem; margin-bottom: 1rem; text-align: center; font-weight: bold;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <label>Usuario</label>
                <input type="text" name="nom_usuario" placeholder="Ingrese su usuario" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Ingrese su contraseña" required>

                <div class="remember-forgot">
                <label class="remember-me">
                <input type="checkbox" name="remember" id="remember">
                <span class="checkmark"></span>
                Recordarme en este equipo
                </label>    
                </div>

                <button type="submit">Iniciar sesión</button>

            </form>

        </div>
    </div>

</div>

</body>
</html>