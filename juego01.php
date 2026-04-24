<?php
session_start();
include("master_array.php"); 

$juego_actual = 1; 
$archivo_puntos = 'puntuaciones.json';
$error = "";
$juego_activo = false;

// FUNCIÓN PARA VERIFICAR SI YA JUGÓ
function yaParticipo($nombre, $clase, $juego, $archivo) {
    if (!file_exists($archivo)) return false;
    $lineas = file($archivo);
    foreach ($lineas as $linea) {
        $registro = json_decode($linea, true);
        if ($registro['nombre'] === $nombre && $registro['clase'] === $clase && $registro['juego'] == $juego) {
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
        $error = "¡$nombre, ya hiciste el Juego 1 de la $clase_sel! ✨";
    } else {
        $juego_activo    = true;
        $palabras        = $clases[$clase_sel];
        $sopa_data       = generarSopa($palabras);
        $nombre_jugador  = $nombre;
        $clase_jugador   = $clase_sel;
        $inicio_tiempo   = time();
    }
}

// GUARDAR EN JSON Y SALTAR
if (isset($_POST['finalizar_juego'])) {
    $registro = [
        "nombre" => $_POST['f_nombre'],
        "clase"  => $_POST['f_clase'],
        "juego"  => (int)$_POST['f_juego'],
        "puntos" => (int)$_POST['puntos'],
        "fecha"  => date("Y-m-d H:i:s")
    ];
    file_put_contents($archivo_puntos, json_encode($registro) . PHP_EOL, FILE_APPEND);
    header("Location: juego02.php"); 
    exit();
}

function generarSopa($palabras) {
    $size  = 10;
    $grid  = array_fill(0, $size, array_fill(0, $size, ''));
    $soluciones = [];
    foreach ($palabras as $p) {
        $colocada = false;
        while (!$colocada) {
            $f    = rand(0, $size - 1);
            $cI   = rand(0, $size - strlen($p));
            $libre = true;
            for ($i = 0; $i < strlen($p); $i++) if ($grid[$f][$cI + $i] !== '') $libre = false;
            if ($libre) {
                $indices = [];
                for ($i = 0; $i < strlen($p); $i++) {
                    $grid[$f][$cI + $i] = $p[$i];
                    $indices[] = $f * $size + ($cI + $i);
                }
                $soluciones[$p] = $indices;
                $colocada = true;
            }
        }
    }
    $letras = "ABCDEFGHIJKLMOPQRSTUVWXYZ";
    for ($f = 0; $f < $size; $f++)
        for ($c = 0; $c < $size; $c++)
            if ($grid[$f][$c] == '') $grid[$f][$c] = $letras[rand(0, 24)];
    return ['grid' => $grid, 'soluciones' => $soluciones];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego 01 – Sopa de Letras 🔍</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Bungee&display=swap" rel="stylesheet">
    <style>
        :root {
            --fondo:   #e0f7fa;
            --rosa:    #ff4081;
            --cian:    #00b8d4;
            --verde:   #76ff03;
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
            max-width: 580px;
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
        .breadcrumb a {
            color: var(--cian);
            text-decoration: none;
            font-weight: bold;
            transition: color 0.2s;
        }
        .breadcrumb a:hover { color: var(--rosa); }
        .breadcrumb .sep { color: #b0bec5; }
        .breadcrumb .actual { color: var(--rosa); font-weight: bold; }

        /* ── CARD ── */
        .card {
            background: white;
            padding: 30px 25px;
            border-radius: 40px;
            box-shadow: 0 12px 0 #b2ebf2;
            text-align: center;
            width: 100%;
            max-width: 580px;
            border: 5px solid white;
            position: relative;
        }

        /* Barra de color superior */
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 8px;
            background: var(--rosa);
            border-radius: 40px 40px 0 0;
        }

        h1 {
            font-family: 'Bungee', cursive;
            font-size: 2.2rem;
            color: var(--rosa);
            text-shadow: 3px 3px 0 #f8bbd0;
            margin: 8px 0 6px;
        }

        .badge-juego {
            display: inline-block;
            background: #fce4ec;
            color: var(--rosa);
            font-family: 'Bungee', cursive;
            font-size: 0.75rem;
            padding: 4px 14px;
            border-radius: 20px;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        /* ── SELECTS ── */
        select {
            width: 85%;
            padding: 13px;
            margin: 8px 0;
            border-radius: 20px;
            border: 3px solid #b2ebf2;
            font-family: inherit;
            font-size: 1.1rem;
            text-align: center;
            outline: none;
            background: white;
            color: var(--texto);
        }

        /* ── BOTÓN ── */
        .btn {
            background: var(--rosa);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-family: 'Bungee', cursive;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 8px 0 #c2185b;
            margin-top: 15px;
            transition: transform 0.1s;
        }
        .btn:active { transform: translateY(4px); box-shadow: 0 4px 0 #c2185b; }

        /* ── SOPA ── */
        .grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 6px;
            background: #fff9c4;
            padding: 12px;
            border-radius: 25px;
            margin: 18px 0;
            border: 4px dashed #ffeb3b;
        }

        .cell {
            background: white;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.15s, background 0.15s;
            color: var(--texto);
            border-bottom: 3px solid #eee;
            user-select: none;
        }
        .cell:hover    { transform: scale(1.12); background: #fffde7; }
        .cell.selected { background: var(--cian); color: white; border-bottom: 3px solid #00838f; }
        .cell.found    { background: var(--verde) !important; color: #1b5e20; border-bottom: 3px solid #64dd17; box-shadow: 0 0 8px rgba(118,255,3,0.5); }

        /* ── LISTA DE PALABRAS ── */
        .word-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            list-style: none;
            padding: 0;
            margin-bottom: 10px;
        }
        .word-item {
            padding: 8px 18px;
            background: #fce4ec;
            border-radius: 20px;
            color: var(--rosa);
            font-size: 0.95rem;
            border: 2px solid #f8bbd0;
            transition: background 0.3s;
        }
        .completed {
            background: var(--verde);
            color: #1b5e20;
            text-decoration: line-through;
            border-color: #64dd17;
        }

        /* ── INFO JUGADOR ── */
        .info-jugador {
            background: #e0f7fa;
            border-radius: 15px;
            padding: 8px 16px;
            font-size: 0.9rem;
            color: #00838f;
            margin-bottom: 12px;
            display: inline-block;
        }

        /* ── NAVEGACIÓN JUEGOS ── */
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
        .nav-juegos a.activo      { background: #fce4ec; color: var(--rosa); border-color: var(--rosa); font-weight: bold; }
        .nav-juegos a.desactivado { opacity: 0.4; pointer-events: none; }

        @media (max-width: 480px) {
            h1 { font-size: 1.7rem; }
            .cell { font-size: 0.85rem; }
        }
    </style>
</head>
<body>

<!-- BREADCRUMB -->
<nav class="breadcrumb">
    <a href="index.php">🏠 Inicio</a>
    <span class="sep">›</span>
    <span class="actual">🔍 Juego 01 – Sopa de Letras</span>
</nav>

<div class="card">

    <?php if (!$juego_activo): ?>

        <span class="badge-juego">JUEGO 01</span>
        <h1>SOPA DE LETRAS 🔍</h1>
        <p style="color:#78909c; margin-bottom:15px;">¡Busca tu nombre en la lista para comenzar! 👇</p>

        <form method="POST">
            <select name="nombre_ingresado" required>
                <option value="" disabled selected>--- ¿Quién eres? ---</option>
                <?php 
                sort($nombres_permitidos); 
                foreach ($nombres_permitidos as $nombre): ?>
                    <option value="<?php echo $nombre; ?>"><?php echo $nombre; ?></option>
                <?php endforeach; ?>
            </select>

            <select name="clase_seleccionada" required>
                <option value="" disabled selected>--- Elige tu clase ---</option>
                <?php foreach ($clases as $nc => $p): ?>
                    <option value="<?php echo $nc; ?>"><?php echo $nc; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <button type="submit" name="entrar" class="btn">¡A JUGAR! 🚀</button>
        </form>

        <?php if ($error): ?>
            <p style="color:var(--rosa); margin-top:12px;"><?php echo $error; ?></p>
        <?php endif; ?>

    <?php else: ?>

        <span class="badge-juego">JUEGO 01</span>
        <h1>SOPA DE LETRAS 🔍</h1>

        <div class="info-jugador">
            👤 <b><?php echo $nombre_jugador; ?></b> &nbsp;|&nbsp; 🏫 <b><?php echo $clase_jugador; ?></b>
        </div>

        <div class="grid">
            <?php
            $i = 0;
            foreach ($sopa_data['grid'] as $fila) {
                foreach ($fila as $letra) {
                    echo "<div class='cell' data-idx='$i' onclick='marcar($i)'>$letra</div>";
                    $i++;
                }
            }
            ?>
        </div>

        <ul class="word-list">
            <?php foreach ($palabras as $p): ?>
                <li class="word-item" id="li-<?php echo $p; ?>"><?php echo $p; ?></li>
            <?php endforeach; ?>
        </ul>

        <form method="POST" id="formFinal">
            <input type="hidden" name="f_nombre" value="<?php echo $nombre_jugador; ?>">
            <input type="hidden" name="f_clase"  value="<?php echo $clase_jugador; ?>">
            <input type="hidden" name="f_juego"  value="<?php echo $juego_actual; ?>">
            <input type="hidden" name="puntos"   id="iptPuntos">
            <input type="hidden" name="finalizar_juego" value="1">
            <button type="button" id="btnNext" class="btn" style="display:none;" onclick="terminar()">
                ¡SIGUIENTE NIVEL! 🚀
            </button>
        </form>

    <?php endif; ?>

    <!-- NAVEGACIÓN ENTRE JUEGOS -->
    <div class="nav-juegos">
        <a href="index.php">🏠 Inicio</a>
        <a href="juego01.php" class="activo">🔍 Juego 01</a>
        <a href="juego02.php" class="desactivado">🎨 Juego 02</a>
        <a href="juego03.php" class="desactivado">🏆 Juego 03</a>
        <a href="puntuaciones.php">🥇 Puntuaciones</a>
    </div>

</div>

<script>
    const soluciones = <?php echo json_encode($sopa_data['soluciones'] ?? []); ?>;
    let encontradas  = 0;
    const inicio     = <?php echo $inicio_tiempo ?? 0; ?>;

    function marcar(idx) {
        const c = document.querySelector(`[data-idx="${idx}"]`);
        if (c.classList.contains('found')) return;
        c.classList.toggle('selected');

        for (const [palabra, indices] of Object.entries(soluciones)) {
            const li  = document.getElementById('li-' + palabra);
            const sel = Array.from(document.querySelectorAll('.cell.selected'))
                             .map(el => parseInt(el.dataset.idx));

            if (indices.every(i => sel.includes(i)) && !li.classList.contains('completed')) {
                indices.forEach(i => {
                    const cell = document.querySelector(`[data-idx="${i}"]`);
                    cell.classList.remove('selected');
                    cell.classList.add('found');
                });
                li.classList.add('completed');
                encontradas++;
                if (encontradas === Object.keys(soluciones).length) {
                    document.getElementById('btnNext').style.display = 'inline-block';
                }
            }
        }
    }

    function terminar() {
        const tiempo = Math.floor(Date.now() / 1000) - inicio;
        document.getElementById('iptPuntos').value = Math.max(100, 2000 - (tiempo * 5));
        document.getElementById('formFinal').submit();
    }
</script>

</body>
</html>