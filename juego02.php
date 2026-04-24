<?php
session_start();
include("master_array.php"); 

$juego_actual   = 2;
$archivo_puntos = 'puntuaciones.json';
$error          = "";
$juego_activo   = false;

// VERIFICAR SI YA JUGÓ
function yaParticipo($nombre, $clase, $juego, $archivo) {
    if (!file_exists($archivo)) return false;
    $lineas = file($archivo);
    foreach ($lineas as $linea) {
        $registro = json_decode($linea, true);
        $j_reg = isset($registro['juego']) ? $registro['juego'] : 0;
        if ($registro['nombre'] === $nombre && $registro['clase'] === $clase && $j_reg == $juego) {
            return true;
        }
    }
    return false;
}

// LÓGICA DE INICIO
if (isset($_POST['entrar'])) {
    $nombre    = $_POST['nombre_ingresado'];
    $clase_sel = $_POST['clase_seleccionada'];

    if (yaParticipo($nombre, $clase_sel, $juego_actual, $archivo_puntos)) {
        $error = "¡$nombre, ya pintaste el dibujo de la $clase_sel! 🎨";
    } else {
        $juego_activo    = true;
        $imagen_a_pintar = $dibujos_clases[$clase_sel];
        $nombre_jugador  = $nombre;
        $clase_jugador   = $clase_sel;
        $inicio_tiempo   = time();
    }
}

// GUARDAR RESULTADOS Y SALTAR
if (isset($_POST['finalizar_juego'])) {
    $registro = [
        "nombre" => $_POST['f_nombre'],
        "clase"  => $_POST['f_clase'],
        "juego"  => (int)$_POST['f_juego'],
        "puntos" => (int)$_POST['puntos'],
        "fecha"  => date("Y-m-d H:i:s")
    ];
    file_put_contents($archivo_puntos, json_encode($registro) . PHP_EOL, FILE_APPEND);

    // Guardar el dibujo como imagen PNG
    if (!empty($_POST['imagen_canvas'])) {
        $carpeta = 'dibujos/';
        if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

        $nombre_archivo = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $_POST['f_nombre'])
                        . '_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $_POST['f_clase'])
                        . '_Juego02.png';

        $img_data = str_replace('data:image/png;base64,', '', $_POST['imagen_canvas']);
        file_put_contents($carpeta . $nombre_archivo, base64_decode($img_data));
    }

    header("Location: juego03.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego 02 – Pintura Mágica 🎨</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Bungee&display=swap" rel="stylesheet">
    <style>
        :root {
            --fondo:  #e0f7fa;
            --rosa:   #ff4081;
            --cian:   #00b8d4;
            --texto:  #37474f;
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
            max-width: 880px;
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

        /* ── CARD ── */
        .card {
            background: white;
            padding: 25px 20px;
            border-radius: 40px;
            box-shadow: 0 12px 0 #81d4fa;
            text-align: center;
            width: 100%;
            max-width: 880px;
            border: 5px solid white;
            position: relative;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 8px;
            background: var(--cian);
            border-radius: 40px 40px 0 0;
        }

        /* ── BADGE ── */
        .badge-juego {
            display: inline-block;
            background: #e0f7fa;
            color: var(--cian);
            font-family: 'Bungee', cursive;
            font-size: 0.75rem;
            padding: 4px 14px;
            border-radius: 20px;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        h1 {
            font-family: 'Bungee', cursive;
            color: var(--cian);
            text-shadow: 3px 3px 0 #b2ebf2;
            margin: 5px 0 10px;
            font-size: 2rem;
        }

        /* ── INFO JUGADOR ── */
        .info-jugador {
            background: #e0f7fa;
            border-radius: 15px;
            padding: 7px 16px;
            font-size: 0.9rem;
            color: #00838f;
            margin-bottom: 14px;
            display: inline-block;
        }

        /* ── SELECTS ── */
        .login-input {
            padding: 13px;
            border-radius: 20px;
            border: 3px solid #b2ebf2;
            margin: 8px 0;
            font-family: inherit;
            font-size: 1.1rem;
            width: 75%;
            text-align: center;
            background: white;
            color: var(--texto);
            outline: none;
        }

        /* ── TOOLBAR ── */
        .toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            background: #f1f8e9;
            padding: 14px;
            border-radius: 20px;
            margin-bottom: 15px;
            border: 2px solid #dcedc8;
        }

        .color-btn {
            width: 40px; height: 40px;
            border-radius: 12px;
            border: 3px solid white;
            cursor: pointer;
            transition: transform 0.15s;
            box-shadow: 0 3px 0 #ccc;
        }
        .color-btn:hover  { transform: scale(1.12); }
        .color-btn.active { border-color: #333; box-shadow: 0 0 10px rgba(0,0,0,0.3); }

        #customColor { width: 40px; height: 40px; border: none; cursor: pointer; background: none; padding: 0; }

        .action-btn {
            background: white;
            border: 2px solid #ccc;
            padding: 8px 15px;
            border-radius: 10px;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background 0.2s;
        }
        .action-btn:hover { background: #eee; }

        /* ── CANVAS ── */
        #canvas-wrapper {
            position: relative;
            margin: 0 auto;
            background: white;
            border: 8px solid #e0f7fa;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        canvas { display: block; cursor: crosshair; touch-action: none; }

        /* ── BOTÓN PRINCIPAL ── */
        .btn {
            background: var(--cian);
            color: white;
            border: none;
            padding: 14px 38px;
            border-radius: 50px;
            font-family: 'Bungee', cursive;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 6px 0 #00838f;
            margin-top: 15px;
            transition: transform 0.1s;
        }
        .btn:active { transform: translateY(4px); box-shadow: 0 2px 0 #00838f; }

        /* ── NAV JUEGOS ── */
        .nav-juegos {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
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
        .nav-juegos a:hover       { background: #e0f7fa; color: var(--cian); border-color: var(--cian); }
        .nav-juegos a.activo      { background: #e0f7fa; color: var(--cian); border-color: var(--cian); font-weight: bold; }
        .nav-juegos a.desactivado { opacity: 0.4; pointer-events: none; }

        /* ── OVERLAY GUARDANDO ── */
        #overlay-guardando {
            display: none;                          /* oculto por defecto */
            position: fixed;
            inset: 0;
            background: rgba(0, 184, 212, 0.92);   /* cian semitransparente */
            z-index: 9999;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }
        #overlay-guardando.visible { display: flex; }

        /* Pincel giratorio */
        .pincel-anim {
            font-size: 5rem;
            animation: girar 1s linear infinite;
        }
        @keyframes girar {
            from { transform: rotate(0deg);   }
            to   { transform: rotate(360deg); }
        }

        .guardando-titulo {
            font-family: 'Bungee', cursive;
            color: white;
            font-size: 1.8rem;
            text-shadow: 3px 3px 0 rgba(0,0,0,0.15);
            text-align: center;
        }
        .guardando-sub {
            color: #e0f7fa;
            font-size: 1.1rem;
            text-align: center;
        }

        /* Barra de progreso animada */
        .barra-wrap {
            width: 280px;
            height: 16px;
            background: rgba(255,255,255,0.3);
            border-radius: 20px;
            overflow: hidden;
        }
        .barra-fill {
            height: 100%;
            width: 0%;
            background: white;
            border-radius: 20px;
            animation: llenar 3s ease-in-out forwards;
        }
        @keyframes llenar {
            0%   { width: 0%;   }
            60%  { width: 75%;  }
            90%  { width: 92%;  }
            100% { width: 98%;  } /* nunca llega al 100% hasta que el server responde */
        }

        @media (max-width: 500px) {
            .login-input { width: 90%; }
            .guardando-titulo { font-size: 1.4rem; }
        }
    </style>
</head>
<body>

<!-- ── OVERLAY DE GUARDANDO (se activa con JS) ── -->
<div id="overlay-guardando">
    <div class="pincel-anim">🎨</div>
    <p class="guardando-titulo">¡Guardando tu obra maestra!</p>
    <p class="guardando-sub">No cierres la pantalla... ✨</p>
    <div class="barra-wrap">
        <div class="barra-fill" id="barraFill"></div>
    </div>
</div>

<!-- BREADCRUMB -->
<nav class="breadcrumb">
    <a href="index.php">🏠 Inicio</a>
    <span class="sep">›</span>
    <a href="juego01.php">🔍 Juego 01</a>
    <span class="sep">›</span>
    <span class="actual">🎨 Juego 02 – Pintura Mágica</span>
</nav>

<div class="card">

    <?php if (!$juego_activo): ?>

        <span class="badge-juego">JUEGO 02</span>
        <h1>PINTURA MÁGICA 🎨</h1>
        <p style="color:#78909c; margin-bottom:15px;">¡Busca tu nombre en la lista para pintar! 👇</p>

        <form method="POST">
            <select name="nombre_ingresado" class="login-input" required>
                <option value="" disabled selected>--- ¿Quién eres? ---</option>
                <?php 
                sort($nombres_permitidos); 
                foreach ($nombres_permitidos as $nombre): ?>
                    <option value="<?php echo $nombre; ?>"><?php echo $nombre; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <select name="clase_seleccionada" class="login-input" required>
                <option value="" disabled selected>--- Elige tu clase ---</option>
                <?php foreach ($dibujos_clases as $nc => $img): ?>
                    <option value="<?php echo $nc; ?>"><?php echo $nc; ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <button type="submit" name="entrar" class="btn">¡A PINTAR! 🖌️</button>
        </form>

        <?php if ($error): ?>
            <p style="color:var(--rosa); margin-top:12px;"><?php echo $error; ?></p>
        <?php endif; ?>

    <?php else: ?>

        <span class="badge-juego">JUEGO 02</span>
        <h1>¡DIBUJA Y COLOREA! 🎨</h1>

        <div class="info-jugador">
            👤 <b><?php echo $nombre_jugador; ?></b> &nbsp;|&nbsp; 🏫 <b><?php echo $clase_jugador; ?></b>
        </div>

        <!-- BARRA DE HERRAMIENTAS -->
        <div class="toolbar">
            <div class="color-btn active" style="background:#ff0000" onclick="pickColor('#ff0000', this)"></div>
            <div class="color-btn" style="background:#0000ff" onclick="pickColor('#0000ff', this)"></div>
            <div class="color-btn" style="background:#00cc00" onclick="pickColor('#00cc00', this)"></div>
            <div class="color-btn" style="background:#ffff00" onclick="pickColor('#ffff00', this)"></div>
            <div class="color-btn" style="background:#ffa500" onclick="pickColor('#ffa500', this)"></div>
            <div class="color-btn" style="background:#9900ff" onclick="pickColor('#9900ff', this)"></div>
            <div class="color-btn" style="background:#000000" onclick="pickColor('#000000', this)"></div>
            <div class="color-btn" style="background:#ffffff; border-color:#ccc;" onclick="pickColor('#ffffff', this)"></div>

            <div class="color-btn" title="Elige tu propio color">
                <input type="color" id="customColor" value="#ff4081" oninput="pickColor(this.value, this.parentElement)">
            </div>

            <button class="action-btn" id="eraserBtn" onclick="pickColor('eraser', this)">🧼 Borrador</button>
            <button class="action-btn" onclick="resetCanvas()">🗑️ Limpiar</button>
        </div>

        <!-- CANVAS -->
        <div id="canvas-wrapper">
            <canvas id="paintCanvas"></canvas>
        </div>

        <!-- FORMULARIO FINAL -->
        <form method="POST" id="formFinal">
            <input type="hidden" name="f_nombre"        value="<?php echo $nombre_jugador; ?>">
            <input type="hidden" name="f_clase"         value="<?php echo $clase_jugador; ?>">
            <input type="hidden" name="f_juego"         value="<?php echo $juego_actual; ?>">
            <input type="hidden" name="puntos"          id="iptPuntos">
            <input type="hidden" name="imagen_canvas"   id="iptImagen">
            <input type="hidden" name="finalizar_juego" value="1">
            <button type="button" class="btn" onclick="terminar()">¡LISTO, TERMINÉ! 🚀</button>
        </form>

    <?php endif; ?>

    <!-- NAVEGACIÓN ENTRE JUEGOS -->
    <div class="nav-juegos">
        <a href="index.php">🏠 Inicio</a>
        <a href="juego01.php">🔍 Juego 01</a>
        <a href="juego02.php" class="activo">🎨 Juego 02</a>
        <a href="juego03.php" class="desactivado">🏆 Juego 03</a>
        <a href="puntuaciones.php">🥇 Puntuaciones</a>
    </div>

</div>

<script>
    const canvas = document.getElementById('paintCanvas');
    const ctx    = canvas ? canvas.getContext('2d') : null;
    let painting    = false;
    let brushColor  = "#ff0000";
    let isEraser    = false;
    const bgImage   = new Image();

    if (canvas) {
        bgImage.src = "<?php echo $imagen_a_pintar ?? ''; ?>";
        bgImage.onload = () => {
            const maxWidth  = Math.min(700, window.innerWidth - 60);
            canvas.width    = maxWidth;
            canvas.height   = (bgImage.height * maxWidth) / bgImage.width;
            ctx.drawImage(bgImage, 0, 0, canvas.width, canvas.height);
        };

        canvas.addEventListener('mousedown',  start);
        canvas.addEventListener('mouseup',    stop);
        canvas.addEventListener('mousemove',  draw);
        canvas.addEventListener('touchstart', (e) => { e.preventDefault(); start(e.touches[0]); });
        canvas.addEventListener('touchend',   stop);
        canvas.addEventListener('touchmove',  (e) => { e.preventDefault(); draw(e.touches[0]); });
    }

    function start(e) { painting = true; draw(e); }
    function stop()   { painting = false; ctx.beginPath(); }

    function draw(e) {
        if (!painting) return;
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX - rect.left) * (canvas.width / rect.width);
        const y = (e.clientY - rect.top)  * (canvas.height / rect.height);
        ctx.lineWidth   = isEraser ? 30 : 12;
        ctx.lineCap     = 'round';
        ctx.strokeStyle = isEraser ? "#ffffff" : brushColor;
        ctx.lineTo(x, y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x, y);
        ctx.save();
        ctx.globalCompositeOperation = 'multiply';
        ctx.drawImage(bgImage, 0, 0, canvas.width, canvas.height);
        ctx.restore();
    }

    function pickColor(color, el) {
        document.querySelectorAll('.color-btn').forEach(d => d.classList.remove('active'));
        document.getElementById('eraserBtn').style.borderColor = "#ccc";
        if (color === 'eraser') {
            isEraser = true;
            document.getElementById('eraserBtn').style.borderColor = "#00b8d4";
        } else {
            isEraser   = false;
            brushColor = color;
            el.classList.add('active');
        }
    }

    function resetCanvas() {
        if (confirm("¿Seguro que quieres borrar todo tu dibujo?")) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(bgImage, 0, 0, canvas.width, canvas.height);
        }
    }

    function terminar() {
        // 1. Calcular puntos
        const seg = Math.floor(Date.now() / 1000) - <?php echo $inicio_tiempo ?? 0; ?>;
        document.getElementById('iptPuntos').value = (seg < 30) ? 1400 : 2000;

        // 2. Capturar el canvas como base64
        document.getElementById('iptImagen').value = canvas.toDataURL('image/png');

        // 3. Mostrar overlay de "guardando" ANTES de enviar
        const overlay = document.getElementById('overlay-guardando');
        overlay.classList.add('visible');

        // 4. Reiniciar la animación de la barra clonando el elemento
        const barraVieja = document.getElementById('barraFill');
        const barraNueva = barraVieja.cloneNode(true);
        barraVieja.parentNode.replaceChild(barraNueva, barraVieja);

        // 5. Enviar el formulario (el servidor redirige a juego03.php al terminar)
        document.getElementById('formFinal').submit();
    }
</script>

</body>
</html>