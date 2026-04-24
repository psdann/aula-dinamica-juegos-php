<?php
include("master_array.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grandes Personajes de la Biblia 📖</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Bungee&display=swap" rel="stylesheet">
    <style>
        :root {
            --fondo:    #e0f7fa;
            --rosa:     #ff4081;
            --cian:     #00b8d4;
            --amarillo: #ffc107;
            --verde:    #00c853;
            --morado:   #7c4dff;
            --texto:    #37474f;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Fredoka One', cursive;
            background: linear-gradient(160deg, #e0f7fa 0%, #e8f5e9 50%, #fce4ec 100%);
            color: var(--texto);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
        }

        /* ── ESTRELLAS DECORATIVAS ── */
        body::before {
            content: '✨ ⭐ 🌟 ✨ ⭐ 🌟 ✨ ⭐ 🌟 ✨ ⭐ 🌟 ✨';
            display: block;
            width: 100%;
            text-align: center;
            font-size: 1.4rem;
            padding: 10px 0 0;
            letter-spacing: 8px;
            opacity: 0.5;
        }

        /* ── HEADER ── */
        header {
            text-align: center;
            padding: 30px 20px 10px;
        }

        .logo-emoji {
            font-size: 5rem;
            display: block;
            animation: flotar 3s ease-in-out infinite;
            line-height: 1;
            margin-bottom: 10px;
        }

        @keyframes flotar {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }

        h1 {
            font-family: 'Bungee', cursive;
            font-size: clamp(1.8rem, 5vw, 3.2rem);
            color: #1565c0;
            text-shadow: 3px 3px 0 #fff, 6px 6px 0 rgba(0,0,0,0.08);
            line-height: 1.1;
        }

        .subtitle {
            font-size: 1.1rem;
            color: #546e7a;
            margin-top: 10px;
            background: white;
            display: inline-block;
            padding: 6px 20px;
            border-radius: 50px;
            box-shadow: 0 4px 0 #b2ebf2;
        }

        /* ── SECCIÓN JUEGOS ── */
        .seccion-titulo {
            font-family: 'Bungee', cursive;
            font-size: 1rem;
            letter-spacing: 3px;
            color: #90a4ae;
            margin: 35px 0 15px;
            text-transform: uppercase;
        }

        .menu-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            padding: 0 20px;
            max-width: 1050px;
            width: 100%;
        }

        /* ── TARJETA JUEGO ── */
        .game-card {
            background: white;
            width: 220px;
            padding: 28px 20px 22px;
            border-radius: 35px;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.25s, box-shadow 0.25s;
            border: 5px solid transparent;
            position: relative;
            overflow: hidden;
        }

        /* Brillo superior */
        .game-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            border-radius: 35px 35px 0 0;
        }

        .game-card:hover {
            transform: translateY(-10px) rotate(-1deg);
        }

        .badge {
            position: absolute;
            top: 14px; right: 14px;
            font-size: 0.7rem;
            font-family: 'Bungee', cursive;
            background: #fff3;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: 1px;
        }

        .icon {
            font-size: 4.5rem;
            margin-bottom: 12px;
            filter: drop-shadow(2px 4px 4px rgba(0,0,0,0.15));
        }

        .card-title {
            font-family: 'Bungee', cursive;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }

        .card-desc {
            font-size: 0.85rem;
            color: #78909c;
            text-align: center;
            line-height: 1.4;
        }

        /* Colores por tarjeta */
        .card-1 {
            border-color: var(--rosa);
            box-shadow: 0 12px 0 #c2185b;
        }
        .card-1::before { background: var(--rosa); }
        .card-1 .card-title { color: var(--rosa); }
        .card-1 .badge { background: #fce4ec; color: var(--rosa); }
        .card-1:hover { box-shadow: 0 6px 0 #c2185b; }

        .card-2 {
            border-color: var(--cian);
            box-shadow: 0 12px 0 #00838f;
        }
        .card-2::before { background: var(--cian); }
        .card-2 .card-title { color: var(--cian); }
        .card-2 .badge { background: #e0f7fa; color: var(--cian); }
        .card-2:hover { box-shadow: 0 6px 0 #00838f; }

        .card-3 {
            border-color: var(--amarillo);
            box-shadow: 0 12px 0 #ff8f00;
        }
        .card-3::before { background: var(--amarillo); }
        .card-3 .card-title { color: #e65100; }
        .card-3 .badge { background: #fff8e1; color: #e65100; }
        .card-3:hover { box-shadow: 0 6px 0 #ff8f00; }

        /* ── SECCIÓN EXTRA (Puntuación + Dibujos) ── */
        .extra-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 0 20px;
            max-width: 600px;
            width: 100%;
        }

        .extra-card {
            background: white;
            flex: 1;
            min-width: 200px;
            padding: 22px 20px;
            border-radius: 30px;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            transition: transform 0.25s;
            border: 4px solid transparent;
        }

        .extra-card:hover { transform: translateY(-6px); }

        .extra-card .icon { font-size: 3rem; margin: 0; }

        .extra-card .card-title { font-size: 1.1rem; margin: 0; }

        .extra-card .card-desc { font-size: 0.8rem; }

        .card-pts {
            border-color: var(--verde);
            box-shadow: 0 10px 0 #00701a;
        }
        .card-pts::before { background: var(--verde); }
        .card-pts .card-title { color: var(--verde); }
        .card-pts:hover { box-shadow: 0 4px 0 #00701a; }

        .card-gal {
            border-color: var(--morado);
            box-shadow: 0 10px 0 #4527a0;
        }
        .card-gal::before { background: var(--morado); }
        .card-gal .card-title { color: var(--morado); }
        .card-gal:hover { box-shadow: 0 4px 0 #4527a0; }

        /* ── FOOTER ── */
        footer {
            margin-top: auto;
            padding: 30px 20px;
            text-align: center;
            color: #90a4ae;
            font-size: 0.85rem;
        }

        .dev-info {
            background: white;
            padding: 10px 25px;
            border-radius: 20px;
            box-shadow: 0 4px 0 #b2ebf2;
            display: inline-block;
        }

        strong { color: #1565c0; }

        /* ── RESPONSIVE ── */
        @media (max-width: 500px) {
            .game-card { width: 85vw; }
        }
    </style>
</head>
<body>

    <header>
        <span class="logo-emoji">📖</span>
        <h1>GRANDES PERSONAJES<br>DE LA BIBLIA</h1>
        <div class="subtitle">¡Aprende, juega y diviértete en este gran torneo!</div>
    </header>

    <!-- JUEGOS -->
    <p class="seccion-titulo">🎮 Los Juegos</p>
    <div class="menu-container">

        <a href="juego01.php" class="game-card card-1">
            <span class="badge">JUEGO 01</span>
            <div class="icon">🔍</div>
            <div class="card-title">SOPA DE LETRAS</div>
            <div class="card-desc">¡Encuentra las palabras ocultas de la Biblia!</div>
        </a>

        <a href="juego02.php" class="game-card card-2">
            <span class="badge">JUEGO 02</span>
            <div class="icon">🎨</div>
            <div class="card-title">PINTURA MÁGICA</div>
            <div class="card-desc">¡Dale color a tus personajes favoritos!</div>
        </a>

        <a href="juego03.php" class="game-card card-3">
            <span class="badge">JUEGO 03</span>
            <div class="icon">🏆</div>
            <div class="card-title">GRAN QUIZ FINAL</div>
            <div class="card-desc">¡Demuestra cuánto has aprendido!</div>
        </a>

    </div>

    <!-- EXTRAS -->
    <p class="seccion-titulo">📊 Más opciones</p>
    <div class="extra-container">

        <a href="puntuaciones.php" class="extra-card card-pts">
            <div class="icon">🥇</div>
            <div class="card-title">PUNTUACIONES</div>
            <div class="card-desc">Ver el ranking de todos los jugadores</div>
        </a>

        <a href="verdibujos.php" class="extra-card card-gal">
            <div class="icon">🖼️</div>
            <div class="card-title">VER DIBUJOS</div>
            <div class="card-desc">Galería de pinturas del juego 02</div>
        </a>

    </div>

    <footer>
        <div class="dev-info">
            Desarrollado por <strong>Daniel Quinde</strong> </strong>
        </div>
    </footer>

</body>
</html>