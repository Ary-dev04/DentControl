<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DentControl | Software para clínicas dentales</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: white;
            background: linear-gradient(135deg, #1d4ed8, #14b8a6);
            overflow-x: hidden;
        }

        /* HEADER & NAVBAR */
        header {
            display: flex;
            justify-content: space-between;
            padding: 25px 60px;
            align-items: center;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-weight: 700;
            font-size: 24px;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 400;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #cbd5e1;
        }

        .btn {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            padding: 14px 28px;
            border-radius: 12px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid white;
        }

        /* HERO */
        .hero {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            padding: 100px 20px;
            gap: 60px;
        }

        .hero-left {
            max-width: 550px;
        }

        .hero-left h1 {
            font-size: 55px;
            line-height: 1.1;
            margin-bottom: 20px;
        }

        .hero-left p {
            font-size: 18px;
            color: #e2e8f0;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .security-badge {
            display: inline-block;
            background: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.3);
        }

        /* SLIDER CON MARCO DE NAVEGADOR */
        .browser-mockup {
            width: 600px;
            background: #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
            transform: rotate(-2deg);
            transition: 0.5s;
        }

        .browser-mockup:hover {
            transform: rotate(0deg) scale(1.02);
        }

        .browser-header {
            height: 30px;
            background: #cbd5e1;
            display: flex;
            align-items: center;
            padding: 0 15px;
            gap: 8px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        .dot.red { background: #ff5f56; }
        .dot.yellow { background: #ffbd2e; }
        .dot.green { background: #27c93f; }

        .slider-container {
            width: 100%;
            overflow: hidden;
        }

        .slider {
            display: flex;
            transition: transform 0.8s ease-in-out;
        }

        .slider img {
            width: 100%;
            flex-shrink: 0;
            display: block;
        }

        /* TRUSTED BY LOGOS */
        .trusted-by {
            text-align: center;
            padding: 20px;
            background: rgba(0,0,0,0.15);
        }
        .trusted-by p { font-size: 14px; text-transform: uppercase; letter-spacing: 2px; color: #cbd5e1;}
        .logos {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 15px;
            font-weight: bold;
            font-size: 20px;
            opacity: 0.7;
        }

        /* SECTION TITLES */
        .section-title {
            text-align: center;
            font-size: 36px;
            margin-bottom: 50px;
            width: 100%;
        }

        /* FEATURES */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 80px 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 16px;
            transition: 0.3s;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        /* TESTIMONIALS */
        .testimonials {
            padding: 80px 40px;
            background: rgba(0,0,0,0.05);
        }
        .test-grid {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }
        .test-card {
            background: white;
            color: #1e293b;
            padding: 30px;
            border-radius: 16px;
            max-width: 320px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .test-card p { font-style: italic; margin-bottom: 20px; }
        .author { display: flex; align-items: center; gap: 15px; }
        .author-icon { width: 40px; height: 40px; background: #3b82f6; border-radius: 50%; display: flex; justify-content: center; align-items: center; color: white; font-weight: bold;}

        /* FINAL CTA */
        .final-cta {
            text-align: center;
            padding: 100px 20px;
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.9), rgba(15, 23, 42, 0.9));
        }
        .final-cta h2 { font-size: 40px; margin-bottom: 20px; }
        .final-cta p { font-size: 18px; margin-bottom: 30px; color: #cbd5e1; }

        /* FOOTER */
        footer {
            background: #0f172a;
            padding: 60px 40px 20px;
        }
        .footer-grid {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
            gap: 40px;
            border-bottom: 1px solid #1e293b;
            padding-bottom: 40px;
        }
        .footer-col { max-width: 300px; }
        .footer-col h3 { font-size: 20px; margin-bottom: 15px; }
        .footer-col p { color: #94a3b8; font-size: 14px; line-height: 1.6; }
        .footer-col ul { list-style: none; padding: 0; }
        .footer-col ul li { margin-bottom: 10px; }
        .footer-col ul a { color: #94a3b8; text-decoration: none; transition: 0.3s; }
        .footer-col ul a:hover { color: white; }
        .footer-bottom { text-align: center; padding-top: 20px; color: #64748b; font-size: 14px; }

        /* SCROLL ANIMATION */
        .fade-up {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s ease;
        }
        .fade-up.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>

<header>
    <div class="logo">DentControl</div>
    <div class="nav-links">
        <a href="#caracteristicas">Características</a>
        <a href="#testimonios">Testimonios</a>
        
        @auth
            @if(auth()->user()->rol === 'superadmin')
                <a href="{{ route('admin.dashboard') }}" class="btn">Dashboard</a>
            @elseif(auth()->user()->rol === 'dentista')
                <a href="{{ route('dentista.dashboard') }}" class="btn">Dashboard</a>
            @elseif(auth()->user()->rol === 'asistente')
                <a href="{{ route('asistente.dashboard') }}" class="btn">Dashboard</a>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn">Iniciar sesión</a>
        @endauth
    </div>
</header>

<section class="hero fade-up">
    <div class="hero-left">
        <div class="security-badge">🔒 Encriptación de nivel médico y respaldo en la nube</div>
        <h1>El sistema operativo de tu clínica dental</h1>
        <p>Automatiza tu agenda, digitaliza tus expedientes clínicos y controla tus finanzas en una sola plataforma profesional y fácil de usar.</p>
        <div style="display: flex; gap: 15px;">
            <a href="{{ route('login') }}" class="btn">Comenzar ahora</a>
            <a href="#caracteristicas" class="btn btn-outline">Ver funciones</a>
        </div>
    </div>

    <div class="browser-mockup">
        <div class="browser-header">
            <div class="dot red"></div>
            <div class="dot yellow"></div>
            <div class="dot green"></div>
        </div>
        <div class="slider-container">
            <div class="slider" id="slider">
                <img src="{{ asset('images/dashboard1.jpeg') }}" alt="DentControl Dashboard">
                <img src="{{ asset('images/dashboard2.jpeg') }}" alt="DentControl Agenda">
            </div>
        </div>
    </div>
</section>

<section class="trusted-by fade-up">
    <p>Con la confianza de clínicas líderes</p>
    <div class="logos">
        <span>OdontoSalud</span>
        <span>Clínica Sonrisas</span>
        <span>DentalCare Pro</span>
        <span>Implantes MX</span>
    </div>
</section>

<section id="caracteristicas" class="features fade-up">
    <h2 class="section-title">Todo lo que necesitas para crecer</h2>
    
    <div class="card">
        <h3>📅 Agenda inteligente</h3>
        <p>Organiza citas automáticamente, envía recordatorios por WhatsApp y reduce las inasistencias de tus pacientes.</p>
    </div>
    <div class="card">
        <h3>🦷 Expediente clínico digital</h3>
        <p>Consulta el historial odontológico, odontogramas y radiografías de forma segura y desde cualquier dispositivo.</p>
    </div>
    <div class="card">
        <h3>💳 Control financiero</h3>
        <p>Administra pagos, abonos de tratamientos, genera presupuestos y ten el control total de los ingresos y egresos.</p>
    </div>
    <div class="card">
        <h3>📊 Analíticas y Reportes</h3>
        <p>Descubre qué tratamientos te generan más ingresos y analiza el rendimiento de cada dentista de tu equipo.</p>
    </div>
</section>

<section id="testimonios" class="testimonials fade-up">
    <h2 class="section-title" style="color:#1e293b;">Lo que dicen nuestros especialistas</h2>
    <div class="test-grid">
        <div class="test-card">
            <p>"DentControl cambió por completo la forma en que administramos la clínica. Perdimos el miedo al papel y ahora todo fluye más rápido."</p>
            <div class="author">
                <div class="author-icon">DR</div>
                <div>
                    <strong>Dr. Roberto Sánchez</strong><br>
                    <span style="font-size: 13px; color: #64748b;">OdontoSalud Center</span>
                </div>
            </div>
        </div>
        <div class="test-card">
            <p>"Los recordatorios de citas nos han ahorrado miles de pesos al mes en cancelaciones. Es una inversión que se paga sola."</p>
            <div class="author">
                <div class="author-icon">AM</div>
                <div>
                    <strong>Dra. Ana Martínez</strong><br>
                    <span style="font-size: 13px; color: #64748b;">Especialista en Ortodoncia</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="final-cta fade-up">
    <h2>¿Listo para modernizar tu clínica dental?</h2>
    <p>Únete a los odontólogos que ya están optimizando su tiempo e ingresos con DentControl.</p>
    <a href="{{ route('login') }}" class="btn" style="font-size: 18px; padding: 18px 40px;">Iniciar sesión</a>
</section>

<footer>
    <div class="footer-grid">
        <div class="footer-col">
            <div class="logo" style="margin-bottom: 15px;">DentControl</div>
            <p>El software de gestión clínica en la nube diseñado específicamente para las necesidades de los odontólogos modernos.</p>
        </div>
        <div class="footer-col">
            <h3>Producto</h3>
            <ul>
                <li><a href="#caracteristicas">Características</a></li>
                <li><a href="#">Actualizaciones</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h3>Soporte</h3>
            <ul>
                <li><a href="#">Centro de ayuda</a></li>
                <li><a href="#">Tutoriales en video</a></li>
                <li><a href="#">Contacto</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h3>Legales</h3>
            <ul>
                <li><a href="{{ route('terminos') }}">Términos y condiciones</a></li>
                <li><a href="{{ route('privacidad') }}">Aviso de privacidad</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2026 DentControl por U-Core Tech. Todos los derechos reservados.</p>
    </div>
</footer>

<script>
document.addEventListener("DOMContentLoaded", () => {
    /* 🎞️ SLIDER AUTOMÁTICO */
    let index = 0;
    const slider = document.getElementById("slider");
    if (slider) {
        const slides = slider.children;
        setInterval(() => {
            index = (index + 1) % slides.length;
            slider.style.transform = `translateX(-${index * 100}%)`;
        }, 3500);
    }

    /* 🎬 SCROLL ANIMATION */
    const elements = document.querySelectorAll('.fade-up');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            }
        });
    }, { threshold: 0.1 });
    elements.forEach(el => observer.observe(el));
});
</script>

</body>
</html>