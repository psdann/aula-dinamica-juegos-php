<?php
/**
 * verdibujos.php – Galería de Dibujos
 * Muestra todos los dibujos guardados en la carpeta /dibujos/
 */

$carpeta = 'dibujos/';
$dibujos = [];

// Obtener todos los archivos PNG/JPG de la carpeta
if (is_dir($carpeta)) {
    $archivos = glob($carpeta . '*.{png,jpg,jpeg}', GLOB_BRACE);
    foreach ($archivos as $archivo) {
        $solo_nombre = pathinfo($archivo, PATHINFO_FILENAME);
        $dibujos[] = [
            'ruta'   => $archivo,
            'titulo' => str_replace('_', ' ', $solo_nombre),
            'raw'    => strtolower($solo_nombre),
            'fecha'  => date("d/m/Y H:i", filemtime($archivo))
        ];
    }
    // Ordenar por más reciente primero
    usort($dibujos, fn($a, $b) => filemtime($b['ruta']) - filemtime($a['ruta']));
}

$total = count($dibujos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de Dibujos 🎨</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Bungee&display=swap" rel="stylesheet">
    <style>
        :root {
            --rosa:    #ff4081;
            --cian:    #00b8d4;
            --amarillo:#ffc107;
            --naranja: #fb8c00;
            --verde:   #00c853;
            --texto:   #37474f;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Fredoka One', cursive;
            background: linear-gradient(160deg, #e0f7fa 0%, #e8f5e9 50%, #fce4ec 100%);
            color: var(--texto);
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 15px 10px 30px;
        }

        /* ── BREADCRUMB ── */
        .breadcrumb {
            width: 100%;
            max-width: 1100px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: #78909c;
            background: white;
            padding: 8px 18px;
            border-radius: 50px;
            box-shadow: 0 4px 0 #b2ebf2;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .breadcrumb a { color: var(--cian); text-decoration: none; font-weight: bold; transition: color 0.2s; }
        .breadcrumb a:hover { color: var(--rosa); }
        .breadcrumb .sep    { color: #b0bec5; }
        .breadcrumb .actual { color: var(--rosa); font-weight: bold; }

        /* ── CARD CONTENEDOR ── */
        .card {
            background: white;
            padding: 28px 22px;
            border-radius: 40px;
            box-shadow: 0 12px 0 #f8bbd0;
            width: 100%;
            max-width: 1100px;
            border: 5px solid white;
            position: relative;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 8px;
            background: linear-gradient(90deg, var(--rosa), var(--amarillo), var(--cian));
            border-radius: 40px 40px 0 0;
        }

        /* ── ENCABEZADO ── */
        .header-galeria {
            text-align: center;
            margin-bottom: 22px;
        }
        .badge-titulo {
            display: inline-block;
            background: #fce4ec;
            color: var(--rosa);
            font-family: 'Bungee', cursive;
            font-size: 0.75rem;
            padding: 4px 14px;
            border-radius: 20px;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        h1 {
            font-family: 'Bungee', cursive;
            color: var(--rosa);
            text-shadow: 3px 3px 0 #f8bbd0;
            font-size: 2rem;
            margin-bottom: 4px;
        }
        .subtitulo { color: #90a4ae; font-size: 0.95rem; }

        /* ── BUSCADOR ── */
        .buscador-wrap {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }
        .buscador-wrap input {
            padding: 11px 22px;
            border-radius: 50px;
            border: 3px solid #f8bbd0;
            font-family: inherit;
            font-size: 1rem;
            width: 100%;
            max-width: 420px;
            outline: none;
            color: var(--texto);
            transition: border-color 0.2s;
        }
        .buscador-wrap input:focus { border-color: var(--rosa); }

        /* ── CONTADOR ── */
        .contador {
            text-align: center;
            color: #90a4ae;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .contador b { color: var(--rosa); }

        /* ── GALERÍA ── */
        .galeria {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 22px;
        }

        /* ── TARJETA ── */
        .tarjeta {
            background: white;
            border-radius: 25px;
            box-shadow: 0 8px 0 #f8bbd0;
            overflow: hidden;
            border: 4px solid #fce4ec;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
        }
        .tarjeta:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 0 #f48fb1;
        }

        .tarjeta-img-wrap {
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        .tarjeta img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            display: block;
            transition: transform 0.3s;
        }
        .tarjeta-img-wrap:hover img { transform: scale(1.05); }

        /* Overlay al pasar el mouse */
        .tarjeta-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 64, 129, 0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.25s;
            font-size: 2.5rem;
        }
        .tarjeta-img-wrap:hover .tarjeta-overlay { opacity: 1; }

        .tarjeta-info {
            padding: 12px 14px 8px;
            flex: 1;
        }
        .tarjeta-info .nombre {
            color: var(--rosa);
            font-size: 0.95rem;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .tarjeta-info .fecha {
            color: #b0bec5;
            font-size: 0.78rem;
        }

        /* Botón descargar */
        .btn-dl {
            display: block;
            text-align: center;
            background: var(--rosa);
            color: white;
            text-decoration: none;
            padding: 9px;
            font-size: 0.88rem;
            border-radius: 0 0 20px 20px;
            transition: background 0.2s;
        }
        .btn-dl:hover { background: #c2185b; }

        /* ── VACÍO ── */
        .vacio {
            grid-column: 1 / -1;
            text-align: center;
            color: #90a4ae;
            font-size: 1.2rem;
            padding: 50px 20px;
        }

        /* ── MODAL LIGHTBOX ── */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.88);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 14px;
            padding: 20px;
        }
        .modal.activo { display: flex; }

        .modal img {
            max-width: 90vw;
            max-height: 75vh;
            border-radius: 20px;
            border: 5px solid white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }
        .modal-titulo {
            color: white;
            font-size: 1.1rem;
            text-align: center;
        }
        .modal-cerrar {
            background: var(--rosa);
            color: white;
            border: none;
            padding: 10px 32px;
            border-radius: 50px;
            font-family: inherit;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 5px 0 #c2185b;
            transition: transform 0.1s;
        }
        .modal-cerrar:active { transform: translateY(3px); box-shadow: 0 2px 0 #c2185b; }

        /* ── NAV JUEGOS ── */
        .nav-juegos {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 24px;
            flex-wrap: wrap;
        }
        .nav-juegos a {
            text-decoration: none;
            background: #f5f5f5;
            color: #90a4ae;
            padding: 7px 18px;
            border-radius: 20px;
            font-size: 0.85rem;
            border: 2px solid #eee;
            transition: all 0.2s;
        }
        .nav-juegos a:hover  { background: #fce4ec; color: var(--rosa); border-color: #f8bbd0; }
        .nav-juegos a.activo { background: #fce4ec; color: var(--rosa); border-color: #f8bbd0; font-weight: bold; }

        @media (max-width: 480px) {
            h1 { font-size: 1.5rem; }
            .galeria { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 14px; }
        }
    </style>
</head>
<body>

<!-- BREADCRUMB -->
<nav class="breadcrumb">
    <a href="index.php">🏠 Inicio</a>
    <span class="sep">›</span>
    <a href="juego02.php">🎨 Juego 02</a>
    <span class="sep">›</span>
    <span class="actual">🖼️ Galería de Dibujos</span>
</nav>

<div class="card">

    <!-- ENCABEZADO -->
    <div class="header-galeria">
        <span class="badge-titulo">GALERÍA</span>
        <h1>🎨 DIBUJOS MÁGICOS</h1>
        <p class="subtitulo">¡Mira todas las obras de arte de los chicos!</p>
    </div>

    <!-- BUSCADOR -->
    <div class="buscador-wrap">
        <input type="text" id="buscar" placeholder="🔍 Buscar por nombre o clase..." oninput="filtrar()">
    </div>

    <!-- CONTADOR -->
    <p class="contador" id="contador">
        <b><?php echo $total; ?></b> dibujo<?php echo $total != 1 ? 's' : ''; ?> encontrado<?php echo $total != 1 ? 's' : ''; ?>
    </p>

    <!-- GALERÍA -->
    <div class="galeria" id="galeria">
        <?php if (empty($dibujos)): ?>
            <div class="vacio">😢 Aún no hay dibujos guardados.</div>
        <?php else: ?>
            <?php foreach ($dibujos as $d): ?>
            <div class="tarjeta" data-titulo="<?php echo $d['raw']; ?>">

                <div class="tarjeta-img-wrap"
                     onclick="abrirModal('<?php echo $d['ruta']; ?>', '<?php echo htmlspecialchars($d['titulo']); ?>')">
                    <img src="<?php echo $d['ruta']; ?>" alt="<?php echo htmlspecialchars($d['titulo']); ?>" loading="lazy">
                    <div class="tarjeta-overlay">🔍</div>
                </div>

                <div class="tarjeta-info">
                    <p class="nombre">🖌️ <?php echo htmlspecialchars($d['titulo']); ?></p>
                    <p class="fecha">📅 <?php echo $d['fecha']; ?></p>
                </div>

                <a class="btn-dl" href="<?php echo $d['ruta']; ?>" download>⬇️ Descargar</a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- NAVEGACIÓN ENTRE JUEGOS -->
    <div class="nav-juegos">
        <a href="index.php">🏠 Inicio</a>
        <a href="juego01.php">🔍 Juego 01</a>
        <a href="juego02.php">🎨 Juego 02</a>
        <a href="juego03.php">🏆 Juego 03</a>
        <a href="puntuaciones.php">🥇 Puntuaciones</a>
        <a href="verdibujos.php" class="activo">🖼️ Galería</a>
    </div>

</div>

<!-- MODAL LIGHTBOX -->
<div class="modal" id="modal" onclick="cerrarModal()">
    <p class="modal-titulo" id="modalTitulo"></p>
    <img id="modalImg" src="" alt="" onclick="event.stopPropagation()">
    <button class="modal-cerrar" onclick="cerrarModal()">✖ Cerrar</button>
</div>

<script>
    // Filtrar tarjetas por búsqueda
    function filtrar() {
        const q        = document.getElementById('buscar').value.toLowerCase();
        const tarjetas = document.querySelectorAll('.tarjeta');
        let visibles   = 0;

        tarjetas.forEach(t => {
            const coincide = t.dataset.titulo.includes(q);
            t.style.display = coincide ? '' : 'none';
            if (coincide) visibles++;
        });

        const c = document.getElementById('contador');
        c.innerHTML = `<b>${visibles}</b> dibujo${visibles !== 1 ? 's' : ''} encontrado${visibles !== 1 ? 's' : ''}`;
    }

    // Abrir lightbox
    function abrirModal(src, titulo) {
        document.getElementById('modalImg').src          = src;
        document.getElementById('modalTitulo').textContent = '🖌️ ' + titulo;
        document.getElementById('modal').classList.add('activo');
        document.body.style.overflow = 'hidden';
    }

    // Cerrar lightbox
    function cerrarModal() {
        document.getElementById('modal').classList.remove('activo');
        document.body.style.overflow = '';
    }

    // Cerrar con ESC
    document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModal(); });
</script>

</body>
</html>