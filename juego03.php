<?php
session_start();
include("master_array.php"); 

$archivo_puntos = 'puntuaciones.json';
$juego_actual   = 3;
$error          = "";
$juego_activo   = false;
$mostrar_final  = false;
$nombre_jugador = ""; 
$clase_jugador  = "";

// VERIFICAR SI YA JUGÓ
function yaParticipo($n, $c, $j, $a) {
    if (!file_exists($a)) return false;
    foreach (file($a) as $l) {
        $r = json_decode($l, true);
        if (isset($r['nombre']) && $r['nombre'] === $n && $r['clase'] === $c && $r['juego'] == $j) return true;
    }
    return false;
}

// CALCULAR RESULTADOS TOTALES
function obtenerResultadosFinales($n, $c, $a) {
    if (!file_exists($a)) return ["suma" => 0, "lista" => []];
    $p = 0; $j = [];
    foreach (file($a) as $l) {
        $r = json_decode($l, true);
        if (isset($r['nombre']) && $r['nombre'] === $n && $r['clase'] === $c) {
            $p += (int)$r['puntos']; $j[] = (int)$r['juego'];
        }
    }
    return ["suma" => $p, "lista" => $j];
}

// LÓGICA DE INICIO
if (isset($_POST['entrar'])) {
    $nombre_jugador = $_POST['nombre_ingresado'];
    $clase_jugador  = $_POST['clase_seleccionada'];
    if (yaParticipo($nombre_jugador, $clase_jugador, $juego_actual, $archivo_puntos)) {
        $mostrar_final = true;
        $res = obtenerResultadosFinales($nombre_jugador, $clase_jugador, $archivo_puntos);
    } else {
        $juego_activo    = true;
        $preguntas_juego = $banco_preguntas[$clase_jugador] ?? $banco_preguntas["Clase 1"];
    }
}

// GUARDAR PUNTOS Y MOSTRAR RESULTADO FINAL
if (isset($_POST['guardar_puntos'])) {
    $nombre_jugador = $_POST['f_nombre'];
    $clase_jugador  = $_POST['f_clase'];
    $registro = [
        "nombre" => $nombre_jugador,
        "clase"  => $clase_jugador,
        "juego"  => 3,
        "puntos" => (int)$_POST['puntos_quiz'],
        "fecha"  => date("Y-m-d H:i:s")
    ];
    file_put_contents($archivo_puntos, json_encode($registro) . PHP_EOL, FILE_APPEND);
    $mostrar_final = true;
    $res = obtenerResultadosFinales($nombre_jugador, $clase_jugador, $archivo_puntos);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego 03 – Quiz Final 🏆</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Bungee&display=swap" rel="stylesheet">
    <style>
        :root {
            --fondo:    #e0f7fa;
            --amarillo: #ffc107;
            --naranja:  #fb8c00;
            --verde:    #00c853;
            --cian:     #00b8d4;
            --rosa:     #ff4081;
            --texto:    #37474f;
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
            max-width: 640px;
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
        .breadcrumb .actual { color: var(--naranja); font-weight: bold; }

        /* ── CARD ── */
        .card {
            background: white;
            padding: 30px 25px;
            border-radius: 40px;
            box-shadow: 0 12px 0 #ffe0b2;
            text-align: center;
            width: 100%;
            max-width: 640px;
            border: 5px solid white;
            position: relative;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 8px;
            background: var(--amarillo);
            border-radius: 40px 40px 0 0;
        }

        /* ── BADGE ── */
        .badge-juego {
            display: inline-block;
            background: #fff8e1;
            color: var(--naranja);
            font-family: 'Bungee', cursive;
            font-size: 0.75rem;
            padding: 4px 14px;
            border-radius: 20px;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        h1 {
            font-family: 'Bungee', cursive;
            color: var(--naranja);
            text-shadow: 3px 3px 0 #ffe0b2;
            margin: 5px 0 10px;
            font-size: 2rem;
        }

        /* ── INFO JUGADOR ── */
        .info-jugador {
            background: #fff8e1;
            border-radius: 15px;
            padding: 7px 16px;
            font-size: 0.9rem;
            color: var(--naranja);
            margin-bottom: 14px;
            display: inline-block;
        }

        /* ── SELECTS ── */
        .login-input {
            padding: 13px;
            border-radius: 20px;
            border: 3px solid #ffe0b2;
            margin: 8px 0;
            font-family: inherit;
            font-size: 1.1rem;
            width: 85%;
            text-align: center;
            background: white;
            color: var(--texto);
            outline: none;
        }

        /* ── PROGRESO ── */
        .progreso-wrap {
            background: #f5f5f5;
            border-radius: 20px;
            height: 12px;
            margin: 10px 0 18px;
            overflow: hidden;
        }
        .progreso-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--amarillo), var(--naranja));
            border-radius: 20px;
            transition: width 0.4s ease;
        }
        .progreso-label {
            font-size: 0.8rem;
            color: #90a4ae;
            margin-bottom: 4px;
        }

        /* ── QUIZ ── */
        #q-title {
            font-family: 'Bungee', cursive;
            color: var(--naranja);
            font-size: 1.3rem;
            margin-bottom: 12px;
        }

        .quiz-option {
            display: block;
            background: #f9f9f9;
            margin: 10px 0;
            padding: 14px 18px;
            border-radius: 20px;
            cursor: pointer;
            border: 3px solid #eee;
            transition: all 0.2s;
            text-align: left;
            font-size: 1rem;
        }
        .quiz-option:hover { background: #fff8e1; border-color: var(--amarillo); }
        .selected { background: #fff3e0 !important; border-color: var(--naranja) !important; color: var(--naranja); }

        /* ── BOTONES ── */
        .btn {
            background: var(--verde);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 50px;
            font-family: 'Bungee', cursive;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 5px 0 #00701a;
            margin: 5px;
            transition: transform 0.1s;
        }
        .btn:active { transform: translateY(3px); box-shadow: 0 2px 0 #00701a; }

        .btn-nav {
            background: #90a4ae;
            box-shadow: 0 5px 0 #546e7a;
        }
        .btn-nav:active { box-shadow: 0 2px 0 #546e7a; }

        .btn-finish {
            background: var(--naranja);
            box-shadow: 0 5px 0 #e64a19;
        }
        .btn-finish:active { box-shadow: 0 2px 0 #e64a19; }

        .btn-inicio {
            background: var(--cian);
            box-shadow: 0 5px 0 #00838f;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            padding: 12px 30px;
            border-radius: 50px;
            font-family: 'Bungee', cursive;
            font-size: 1rem;
            color: white;
        }

        /* ── SCORE BOX ── */
        .score-box {
            background: #e8f5e9;
            padding: 22px;
            border-radius: 25px;
            border: 4px dashed #4caf50;
            margin: 15px 0;
        }
        .score-box p { margin: 6px 0; font-size: 1rem; }
        .score-box hr { border: none; border-top: 2px dashed #a5d6a7; margin: 12px 0; }

        .nota-grande {
            font-family: 'Bungee', cursive;
            font-size: 3.5rem;
            color: var(--verde);
            line-height: 1;
            margin: 10px 0 5px;
        }

        .juegos-chips {
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .chip {
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            background: #c8e6c9;
            color: #2e7d32;
            border: 2px solid #a5d6a7;
        }
        .chip.falta {
            background: #fce4ec;
            color: var(--rosa);
            border-color: #f8bbd0;
        }

        /* ── NAV JUEGOS ── */
        .nav-juegos {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 22px;
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
        .nav-juegos a:hover       { background: #fff8e1; color: var(--naranja); border-color: var(--amarillo); }
        .nav-juegos a.activo      { background: #fff8e1; color: var(--naranja); border-color: var(--amarillo); font-weight: bold; }
        .nav-juegos a.desactivado { opacity: 0.4; pointer-events: none; }

        @media (max-width: 480px) {
            h1 { font-size: 1.6rem; }
            .nota-grande { font-size: 2.5rem; }
        }
    </style>
</head>
<body>

<!-- BREADCRUMB -->
<nav class="breadcrumb">
    <a href="index.php">🏠 Inicio</a>
    <span class="sep">›</span>
    <a href="juego01.php">🔍 Juego 01</a>
    <span class="sep">›</span>
    <a href="juego02.php">🎨 Juego 02</a>
    <span class="sep">›</span>
    <span class="actual">🏆 Juego 03 – Quiz Final</span>
</nav>

<div class="card">

    <?php if (!$juego_activo && !$mostrar_final): ?>

        <!-- PANTALLA DE INICIO -->
        <span class="badge-juego">JUEGO 03</span>
        <h1>QUIZ FINAL 🏆</h1>
        <p style="color:#78909c; margin-bottom:15px;">¡Busca tu nombre en la lista para comenzar! 👇</p>

        <form method="POST">
            <select name="nombre_ingresado" class="login-input" required>
                <option value="" disabled selected>--- ¿Quién eres? ---</option>
                <?php 
                sort($nombres_permitidos); 
                foreach ($nombres_permitidos as $nombre): ?>
                    <option value="<?php echo $nombre; ?>"><?php echo $nombre; ?></option>
                <?php endforeach; ?>
            </select>

            <select name="clase_seleccionada" class="login-input" required>
                <option value="" disabled selected>--- Elige tu clase ---</option>
                <?php foreach ($clases as $nc => $p): ?>
                    <option value="<?php echo $nc; ?>"><?php echo $nc; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <button type="submit" name="entrar" class="btn">¡EMPEZAR! 🚀</button>
        </form>

        <?php if ($error): ?>
            <p style="color:var(--rosa); margin-top:12px;"><?php echo $error; ?></p>
        <?php endif; ?>

    <?php elseif ($juego_activo): ?>

        <!-- PANTALLA DEL QUIZ -->
        <span class="badge-juego">JUEGO 03</span>

        <div class="info-jugador">
            👤 <b><?php echo $nombre_jugador; ?></b> &nbsp;|&nbsp; 🏫 <b><?php echo $clase_jugador; ?></b>
        </div>

        <!-- BARRA DE PROGRESO -->
        <p class="progreso-label" id="progreso-label">Pregunta 1 de <?php echo count($preguntas_juego); ?></p>
        <div class="progreso-wrap">
            <div class="progreso-bar" id="progreso-bar" style="width: 0%"></div>
        </div>

        <h2 id="q-title">Pregunta 1</h2>

        <div id="quiz-container">
            <?php foreach ($preguntas_juego as $id => $p): ?>
                <div class="question-block" id="q-block-<?php echo $id; ?>" style="display:none;">
                    <p style="font-size:1.05rem; margin-bottom:10px;"><b><?php echo $p['q']; ?></b></p>
                    <?php foreach ($p['options'] as $oid => $opt): ?>
                        <div class="quiz-option" id="opt-<?php echo $id.'-'.$oid; ?>" onclick="choose(<?php echo $id; ?>, <?php echo $oid; ?>)">
                            <?php echo $opt; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top:20px;">
            <button type="button" class="btn btn-nav" id="btnPrev" onclick="changeQ(-1)" style="display:none;">⬅ Anterior</button>
            <button type="button" class="btn btn-nav" id="btnNext" onclick="changeQ(1)">Siguiente ➡</button>
            <button type="button" class="btn btn-finish" id="btnFinish" onclick="confirmarEnvio()" style="display:none;">¡TERMINAR Y ENVIAR! 🚀</button>
        </div>

        <form method="POST" id="formFinal" style="display:none;">
            <input type="hidden" name="f_nombre"      value="<?php echo $nombre_jugador; ?>">
            <input type="hidden" name="f_clase"       value="<?php echo $clase_jugador; ?>">
            <input type="hidden" name="puntos_quiz"   id="iptPuntos">
            <input type="hidden" name="guardar_puntos" value="1">
        </form>

    <?php elseif ($mostrar_final): ?>

        <!-- PANTALLA DE RESULTADOS -->
        <span class="badge-juego">RESULTADOS FINALES</span>
        <h1>¡TERMINASTE! 🎉</h1>

        <div class="score-box">
            <p>👤 Alumno: <b><?php echo $nombre_jugador; ?></b></p>
            <p>🏫 Clase: <b><?php echo $clase_jugador; ?></b></p>
            <hr>
            <?php
                $faltan = [];
                for ($i = 1; $i <= 3; $i++)
                    if (!in_array($i, $res['lista'])) $faltan[] = $i;

                if (empty($faltan)):
                    $nota = round(($res['suma'] / 6000) * 100);
            ?>
                <div class="nota-grande"><?php echo $nota; ?><span style="font-size:1.5rem;">/100</span></div>
                <p style="color:var(--verde); font-size:1.1rem;">🌟 ¡Eres un experto en la Biblia!</p>
                <div class="juegos-chips">
                    <span class="chip">✅ Juego 01</span>
                    <span class="chip">✅ Juego 02</span>
                    <span class="chip">✅ Juego 03</span>
                </div>
            <?php else: ?>
                <h2 style="color:var(--amarillo); font-family:'Bungee',cursive;">¡CASI LISTO!</h2>
                <p style="margin-top:8px;">Aún te faltan estos juegos:</p>
                <div class="juegos-chips">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <span class="chip <?php echo in_array($i, $faltan) ? 'falta' : ''; ?>">
                            <?php echo in_array($i, $faltan) ? "❌" : "✅"; ?> Juego 0<?php echo $i; ?>
                        </span>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

        <a href="puntuaciones.php" class="btn-inicio">🥇 Ver Puntuaciones</a>
        <br>
        <a href="index.php" class="btn-inicio" style="background:#90a4ae; box-shadow:0 5px 0 #546e7a; margin-top:8px;">🏠 Volver al Inicio</a>

    <?php endif; ?>

    <!-- NAVEGACIÓN ENTRE JUEGOS -->
    <div class="nav-juegos">
        <a href="index.php">🏠 Inicio</a>
        <a href="juego01.php">🔍 Juego 01</a>
        <a href="juego02.php">🎨 Juego 02</a>
        <a href="juego03.php" class="activo">🏆 Juego 03</a>
        <a href="puntuaciones.php">🥇 Puntuaciones</a>
    </div>

</div>

<script>
    let current       = 0;
    const total       = <?php echo isset($preguntas_juego) ? count($preguntas_juego) : 0; ?>;
    const correctas   = <?php echo isset($preguntas_juego) ? json_encode(array_column($preguntas_juego, 'correct')) : '[]'; ?>;
    let respuestasUser = new Array(total).fill(null);

    // Mostrar pregunta actual y actualizar progreso
    function showQ() {
        document.querySelectorAll('.question-block').forEach(b => b.style.display = 'none');
        document.getElementById(`q-block-${current}`).style.display = 'block';
        document.getElementById('q-title').innerText = `Pregunta ${current + 1}`;

        // Barra de progreso
        const pct = Math.round(((current + 1) / total) * 100);
        document.getElementById('progreso-bar').style.width   = pct + '%';
        document.getElementById('progreso-label').innerText   = `Pregunta ${current + 1} de ${total}`;

        document.getElementById('btnPrev').style.display   = current === 0           ? 'none'         : 'inline-block';
        document.getElementById('btnNext').style.display   = current === total - 1   ? 'none'         : 'inline-block';
        document.getElementById('btnFinish').style.display = current === total - 1   ? 'inline-block' : 'none';
    }

    function choose(qIdx, oIdx) {
        respuestasUser[qIdx] = oIdx;
        document.querySelectorAll(`#q-block-${qIdx} .quiz-option`).forEach(o => o.classList.remove('selected'));
        document.getElementById(`opt-${qIdx}-${oIdx}`).classList.add('selected');
    }

    function changeQ(dir) {
        current += dir;
        showQ();
    }

    function confirmarEnvio() {
        if (respuestasUser.includes(null)) {
            alert("¡Espera! Aún te faltan preguntas por responder.");
            return;
        }
        if (confirm("¿Estás seguro de que quieres enviar tus respuestas? Ya no podrás cambiarlas.")) {
            let aciertos = 0;
            respuestasUser.forEach((resp, i) => { if (resp === correctas[i]) aciertos++; });
            const pts = Math.round((aciertos / total) * 2000);
            document.getElementById('iptPuntos').value = pts;
            document.getElementById('formFinal').submit();
        }
    }

    if (total > 0) showQ();
</script>

</body>
</html>