<?php
session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
set_time_limit(0);
error_reporting(E_ALL);

include __DIR__ . '/includes/path_helper.php';
$basePath = getAdminBasePath();
?>
<!DOCTYPE html>
<html lang="es-AR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Racing Gaming · Admin</title>
    <link rel="icon" type="image/png" href="<?= $basePath ?>img/logo.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:           #04081f;
            --bg-alt:       #070d2c;
            --surface:      #0b143d;
            --line:         rgba(135,206,235,0.12);
            --line-strong:  rgba(135,206,235,0.22);
            --celeste:      #75AADB;
            --celeste-bright: #66E0FF;
            --white:        #ffffff;
            --muted:        #8995c2;
            --tech:         'Rajdhani', 'Inter', sans-serif;
            --display:      'Bebas Neue', sans-serif;
            --body:         'Inter', system-ui, sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%;
            font-family: var(--body);
            background: var(--bg);
            color: var(--white);
            -webkit-font-smoothing: antialiased;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background:
                radial-gradient(circle at 20% 50%, rgba(117,170,219,0.08), transparent 45%),
                radial-gradient(circle at 80% 20%, rgba(102,224,255,0.05), transparent 40%),
                var(--bg);
        }

        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 24px;
        }

        .login-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            justify-content: center;
            margin-bottom: 40px;
            text-decoration: none;
            color: var(--white);
        }
        .brand-text {
            font-family: var(--display);
            font-size: 26px;
            letter-spacing: .06em;
            line-height: .9;
        }
        .brand-text small {
            display: block;
            font-family: var(--tech);
            font-weight: 600;
            font-size: 11px;
            letter-spacing: .32em;
            color: var(--celeste);
            margin-top: 3px;
            text-transform: uppercase;
        }

        .login-card {
            background: var(--surface);
            border: 1px solid var(--line-strong);
            border-radius: 6px;
            padding: 40px 36px;
            backdrop-filter: blur(20px);
        }

        .login-eyebrow {
            font-family: var(--tech);
            font-weight: 600;
            font-size: 11px;
            letter-spacing: .32em;
            color: var(--celeste);
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 8px;
        }
        .login-title {
            font-family: var(--display);
            font-size: 32px;
            letter-spacing: .04em;
            text-align: center;
            margin-bottom: 32px;
            color: var(--white);
        }

        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-family: var(--tech);
            font-weight: 600;
            font-size: 12px;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            padding: 14px 16px;
            background: rgba(4,8,31,0.6);
            border: 1px solid var(--line-strong);
            border-radius: 4px;
            color: var(--white);
            font-family: var(--body);
            font-size: 15px;
            transition: border-color .2s;
            outline: none;
        }
        .form-control::placeholder { color: var(--muted); }
        .form-control:focus { border-color: var(--celeste); }

        #mensaje {
            min-height: 20px;
            margin-bottom: 16px;
            font-family: var(--tech);
            font-size: 13px;
            letter-spacing: .06em;
            text-align: center;
            color: #ff6b7a;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--celeste);
            color: var(--bg);
            font-family: var(--tech);
            font-weight: 700;
            font-size: 13px;
            letter-spacing: .22em;
            text-transform: uppercase;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background .2s, transform .15s;
        }
        .btn-login:hover {
            background: var(--celeste-bright);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0); }

        .login-footer {
            margin-top: 24px;
            text-align: center;
            font-family: var(--tech);
            font-size: 11px;
            letter-spacing: .14em;
            color: var(--muted);
            text-transform: uppercase;
        }
        .login-footer a {
            color: var(--celeste);
            text-decoration: none;
        }
        .login-footer a:hover { color: var(--celeste-bright); }
    </style>
</head>
<body>

<div class="login-wrap">
    <a href="../" class="login-brand">
        <img src="<?= $basePath ?>img/logo.svg" alt="Racing Gaming" style="height:48px;width:auto">
        <span class="brand-text">Racing<br>Gaming<small>Panel de administración</small></span>
    </a>

    <div class="login-card">
        <div class="login-eyebrow">Acceso restringido</div>
        <h1 class="login-title">Iniciar sesión</h1>

        <div class="form-group">
            <label for="inputPassword">Contraseña</label>
            <input type="password" class="form-control" id="inputPassword"
                   placeholder="Ingresá tu contraseña"
                   onkeydown="if(event.key==='Enter') loginAdm()">
        </div>

        <div id="mensaje"></div>

        <button class="btn-login" onclick="loginAdm()">Entrar →</button>
    </div>

    <div class="login-footer">
        <a href="../">← Volver a la tienda</a>
    </div>
</div>

<script>
function loginAdm() {
    const pass = document.getElementById('inputPassword').value.trim();
    const mensajeDiv = document.getElementById('mensaje');
    mensajeDiv.innerHTML = '';

    if (pass === '') {
        mensajeDiv.textContent = 'Por favor, ingresá la contraseña.';
        return;
    }

    var basePath = window.location.pathname;
    var adminPos = basePath.lastIndexOf('/admin');
    if (adminPos !== -1) {
        basePath = basePath.substring(0, adminPos + 6);
    } else {
        basePath = basePath.substring(0, basePath.lastIndexOf('/') + 1);
    }
    if (basePath[basePath.length - 1] !== '/') basePath += '/';

    fetch(basePath + 'includes/login_js.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pass })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok === 1) {
            window.location.href = basePath + 'index.php';
        } else {
            mensajeDiv.textContent = data.err || 'Contraseña incorrecta.';
        }
    })
    .catch(() => {
        mensajeDiv.textContent = 'Error de conexión. Intentá de nuevo.';
    });
}
</script>

</body>
</html>
