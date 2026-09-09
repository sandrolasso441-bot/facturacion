<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Sistema de Facturación</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">

    <!-- Estilos Temáticos: Negro + Naranja con Fondo Elegante -->
    <style>
        :root {
            --brand-orange: #ff6b00;
            --brand-orange-hover: #e05d00;
            --orange-glow: rgba(255, 107, 0, 0.35);
            --dark-bg: #0d0e12;
            --dark-card: rgba(18, 20, 26, 0.82);
            --input-bg: rgba(255, 255, 255, 0.05);
            --input-border: rgba(255, 255, 255, 0.12);
        }

        body.login-page {
            position: relative;
            background-color: var(--dark-bg) !important;
            min-height: 100vh;
            overflow-x: hidden;
            font-family: 'Source Sans 3', sans-serif;
            color: #e4e6eb;
        }

        /* Capa de imagen de fondo difuminada con sobrecapa oscura */
        body.login-page::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: linear-gradient(135deg, rgba(13, 14, 18, 0.88), rgba(20, 10, 5, 0.85)), url('<?= base_url("assets/img/background.png") ?>'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(6px);
            transform: scale(1.05);
            z-index: -2;
        }

        /* Resplandor ambiental flotante */
        .ambient-glow {
            position: fixed;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, var(--orange-glow) 0%, rgba(0,0,0,0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            pointer-events: none;
            filter: blur(50px);
        }

        .login-box {
            position: relative;
            z-index: 1;
        }

        /* Tarjeta Oscura con efecto Cristal (Glassmorphism) */
        .card {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background-color: var(--dark-card) !important;
            border: 1px solid rgba(255, 107, 0, 0.2) !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7),
                        0 0 25px rgba(255, 107, 0, 0.12) !important;
        }

        /* Estilos de Texto e Ícono del Título */
        .brand-text {
            color: #ffffff !important;
            letter-spacing: 0.5px;
        }

        .brand-icon {
            color: var(--brand-orange) !important;
            filter: drop-shadow(0 0 8px var(--orange-glow));
        }

        /* Controles e Inputs */
        .form-label {
            color: #b0b3b8 !important;
            font-weight: 500;
        }

        .input-group-text {
            background-color: var(--input-bg) !important;
            border-color: var(--input-border) !important;
            color: var(--brand-orange) !important;
        }

        .form-control {
            background-color: var(--input-bg) !important;
            border-color: var(--input-border) !important;
            color: #ffffff !important;
        }

        .form-control::placeholder {
            color: #72767d !important;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.08) !important;
            border-color: var(--brand-orange) !important;
            box-shadow: 0 0 12px var(--orange-glow) !important;
            color: #ffffff !important;
        }

        .form-control:focus + .input-group-text,
        .input-group:focus-within .input-group-text {
            border-color: var(--brand-orange) !important;
            color: #ffffff !important;
            background-color: var(--brand-orange) !important;
        }

        /* Botón de Ingreso Naranja */
        .btn-orange {
            background: linear-gradient(135deg, var(--brand-orange), #ff3300) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 6px 18px var(--orange-glow);
            transition: all 0.3s ease;
        }

        .btn-orange:hover {
            background: linear-gradient(135deg, #ff7a1a, #ff451a) !important;
            box-shadow: 0 8px 24px rgba(255, 107, 0, 0.5);
            transform: translateY(-1px);
        }

        .btn-orange:active {
            transform: translateY(0);
        }

        /* Alerta personalizada */
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2) !important;
            border-color: rgba(220, 53, 69, 0.4) !important;
            color: #ff8585 !important;
        }
    </style>
</head>
<body class="login-page d-flex align-items-center justify-content-center min-vh-100">
    
    <!-- Resplandor de fondo -->
    <div class="ambient-glow"></div>

    <div class="login-box w-100 p-3" style="max-width: 400px;">
        <div class="login-logo text-center mb-4">
            <a href="#" class="h2 text-decoration-none fw-bold brand-text">
                <i class="bi bi-receipt brand-icon me-2"></i>Facturación App
            </a>
        </div>
        
        <div class="card border-0 rounded-4">
            <div class="card-body login-card-body p-4">
                <p class="login-box-msg text-center text-secondary-emphasis mb-4" style="color: #a0a5b1 !important;">
                    Ingresa tus credenciales para iniciar sesión
                </p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('login/authenticate') ?>" method="post" autocomplete="off">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label small">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" id="username" class="form-control" placeholder="admin" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label small">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-orange fw-semibold py-2 rounded-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <p class="text-center text-secondary small mt-4" style="color: #8a8f9d !important;">
            &copy; <?= date('Y') ?> Sistema de Facturación
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/adminlte/dist/js/adminlte.min.js') ?>"></script>
</body>
</html>