<?php
include("master_array.php");
$archivo_puntos = 'puntuaciones.json';

// LEER Y PROCESAR LOS DATOS DEL JSON
$ranking = [];

if (file_exists($archivo_puntos)) {
    $lineas = file($archivo_puntos);
    foreach ($lineas as $linea) {
        $reg    = json_decode($linea, true);
        $nombre = strtoupper($reg['nombre']);
        $clase  = $reg['clase'];
        $juego  = $reg['juego'];
        $pts    = (int)$reg['puntos'];

        // Llave única por alumno y clase
        $id_alumno = $nombre . " (" . $clase . ")";

        if (!isset($ranking[$id_alumno])) {
            $ranking[$id_alumno] = [
                'nombre'        => $nombre,
                'clase'         => $clase,
                'juego1'        => 0,
                'juego2'        => 0,
                'juego3'        => 0,
                'total_puntos'  => 0,
                'completados'   => 0
            ];
        }

        // Asignar puntos según el juego
        if ($juego == 1) $ranking[$id_alumno]['juego1'] = $pts;
        if ($juego == 2) $ranking[$id_alumno]['juego2'] = $pts;
        if ($juego == 3) $ranking[$id_alumno]['juego3'] = $pts;

        // Recalcular total y juegos completados
        $ranking[$id_alumno]['total_puntos'] =
            $ranking[$id_alumno]['juego1'] +
            $ranking[$id_alumno]['juego2'] +
            $ranking[$id_alumno]['juego3'];

        $conteo = 0;
        if ($ranking[$id_alumno]['juego1'] > 0) $conteo++;
        if ($ranking[$id_alumno]['juego2'] > 0) $conteo++;
        if ($ranking[$id_alumno]['juego3'] > 0) $conteo++;
        $ranking[$id_alumno]['completados'] = $conteo;
    }
}

// ORDENAR POR PUNTAJE TOTAL (MAYOR A MENOR)
uasort($ranking, function($a, $b) {
    return $b['total_puntos'] <=> $a['total_puntos'];
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puntuaciones – Tabla de Posiciones 🏆</title>
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
            max-width: 960px;
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
        .breadcrumb .actual { color: var(--amarillo); font-weight: bold; }

        /* ── CARD ── */
        .card {
            background: white;
            padding: 28px 22px;
            border-radius: 40px;
            box-shadow: 0 12px 0 #ffe0b2;
            width: 100%;
            max-width: 960px;
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
        .header-ranking {
            text-align: center;
            margin-bottom: 22px;
        }
        .badge-titulo {
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
            font-size: 2rem;
            margin-bottom: 4px;
        }
        .subtitulo { color: #90a4ae; font-size: 0.95rem; }

        /* ── FILTRO ── */
        .filtro-wrap {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }
        .filtro-wrap select {
            padding: 10px 18px;
            border-radius: 20px;
            border: 3px solid #ffe0b2;
            font-family: inherit;
            font-size: 0.95rem;
            background: white;
            color: var(--texto);
            outline: none;
            cursor: pointer;
        }

        /* ── TABLA ── */
        .tabla-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        thead th {
            background: linear-gradient(90deg, var(--naranja), var(--amarillo));
            color: white;
            padding: 14px 10px;
            font-family: 'Bungee', cursive;
            font-size: 0.85rem;
            text-align: center;
        }
        thead th:first-child { border-radius: 15px 0 0 0; }
        thead th:last-child  { border-radius: 0 15px 0 0; }

        tbody tr {
            border-bottom: 2px solid #f5f5f5;
            transition: background 0.2s;
        }
        tbody tr:hover { background: #fffde7; }

        td {
            padding: 13px 10px;
            text-align: center;
            font-size: 0.95rem;
        }
        td.nombre-col { text-align: left; font-size: 1rem; }

        /* ── POSICIÓN ── */
        .pos-1 { font-family: 'Bungee', cursive; font-size: 1.6rem; color: #ffd700; }
        .pos-2 { font-family: 'Bungee', cursive; font-size: 1.4rem; color: #b0bec5; }
        .pos-3 { font-family: 'Bungee', cursive; font-size: 1.3rem; color: #cd7f32; }
        .pos-n { font-family: 'Bungee', cursive; font-size: 1.1rem; color: #90a4ae; }

        /* ── BADGES JUEGOS ── */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            color: white;
            font-weight: bold;
        }
        .bg-j1 { background: var(--rosa); }
        .bg-j2 { background: var(--cian); }
        .bg-j3 { background: var(--amarillo); color: #333; }
        .bg-cero { background: #eceff1; color: #b0bec5; }

        /* ── TOTAL Y NOTA ── */
        .total-pts {
            font-family: 'Bungee', cursive;
            color: var(--verde);
            font-size: 1.1rem;
        }
        .nota-chip {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-family: 'Bungee', cursive;
            font-size: 0.9rem;
            color: white;
        }
        .nota-alta   { background: var(--verde); }
        .nota-media  { background: var(--amarillo); color: #333; }
        .nota-baja   { background: var(--rosa); }

        /* ── PROGRESO COMPLETADOS ── */
        .prog-chips { display: flex; gap: 4px; justify-content: center; }
        .prog-dot {
            width: 12px; height: 12px;
            border-radius: 50%;
            background: #eceff1;
        }
        .prog-dot.done { background: var(--verde); }

        /* ── VACÍO ── */
        .vacio {
            text-align: center;
            padding: 40px 20px;
            color: #90a4ae;
            font-size: 1.1rem;
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
        .nav-juegos a:hover  { background: #fff8e1; color: var(--naranja); border-color: var(--amarillo); }
        .nav-juegos a.activo { background: #fff8e1; color: var(--naranja); border-color: var(--amarillo); font-weight: bold; }

        @media (max-width: 500px) {
            h1 { font-size: 1.5rem; }
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
    <a href="juego03.php">🏆 Juego 03</a>
    <span class="sep">›</span>
    <span class="actual">🥇 Puntuaciones</span>
</nav>

<div class="card">

    <!-- ENCABEZADO -->
    <div class="header-ranking">
        <span class="badge-titulo">RANKING GENERAL</span>
        <h1>TABLA DE POSICIONES 🏆</h1>
        <p class="subtitulo">Ranking de los chicos de Coros y Panderos</p>
    </div>

    <!-- FILTRO POR CLASE -->
    <div class="filtro-wrap">
        <select id="filtroClase" onchange="filtrarTabla()">
            <option value="">🏫 Todas las clases</option>
            <?php foreach ($clases as $nc => $p): ?>
                <option value="<?php echo $nc; ?>"><?php echo $nc; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- TABLA -->
    <div class="tabla-wrap">
        <table id="tablaRanking">
            <thead>
                <tr>
                    <th>Pos</th>
                    <th style="text-align:left;">Alumno</th>
                    <th>Clase</th>
                    <th>J1 🔍</th>
                    <th>J2 🎨</th>
                    <th>J3 🏆</th>
                    <th>✅</th>
                    <th>Total</th>
                    <th>Nota /100</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $posicion = 1;
                foreach ($ranking as $alumno):
                    $nota_final = round(($alumno['total_puntos'] / 6000) * 100);
                    $clase_nota = $nota_final >= 70 ? 'nota-alta' : ($nota_final >= 40 ? 'nota-media' : 'nota-baja');

                    // Icono de posición
                    if ($posicion === 1)      $pos_class = 'pos-1';
                    elseif ($posicion === 2)  $pos_class = 'pos-2';
                    elseif ($posicion === 3)  $pos_class = 'pos-3';
                    else                      $pos_class = 'pos-n';

                    $medalla = $posicion === 1 ? '🥇' : ($posicion === 2 ? '🥈' : ($posicion === 3 ? '🥉' : '#' . $posicion));
                ?>
                <tr data-clase="<?php echo $alumno['clase']; ?>">
                    <td><span class="<?php echo $pos_class; ?>"><?php echo $medalla; ?></span></td>
                    <td class="nombre-col"><strong><?php echo $alumno['nombre']; ?></strong></td>
                    <td><?php echo $alumno['clase']; ?></td>
                    <td>
                        <span class="badge <?php echo $alumno['juego1'] > 0 ? 'bg-j1' : 'bg-cero'; ?>">
                            <?php echo $alumno['juego1'] > 0 ? $alumno['juego1'] : '—'; ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?php echo $alumno['juego2'] > 0 ? 'bg-j2' : 'bg-cero'; ?>">
                            <?php echo $alumno['juego2'] > 0 ? $alumno['juego2'] : '—'; ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?php echo $alumno['juego3'] > 0 ? 'bg-j3' : 'bg-cero'; ?>">
                            <?php echo $alumno['juego3'] > 0 ? $alumno['juego3'] : '—'; ?>
                        </span>
                    </td>
                    <td>
                        <div class="prog-chips">
                            <div class="prog-dot <?php echo $alumno['juego1'] > 0 ? 'done' : ''; ?>"></div>
                            <div class="prog-dot <?php echo $alumno['juego2'] > 0 ? 'done' : ''; ?>"></div>
                            <div class="prog-dot <?php echo $alumno['juego3'] > 0 ? 'done' : ''; ?>"></div>
                        </div>
                    </td>
                    <td class="total-pts"><?php echo $alumno['total_puntos']; ?></td>
                    <td><span class="nota-chip <?php echo $clase_nota; ?>"><?php echo $nota_final; ?></span></td>
                </tr>
                <?php $posicion++; endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($ranking)): ?>
            <div class="vacio">😔 Aún no hay registros de juegos completados.</div>
        <?php endif; ?>
    </div>

    <!-- NAVEGACIÓN ENTRE JUEGOS -->
    <div class="nav-juegos">
        <a href="index.php">🏠 Inicio</a>
        <a href="juego01.php">🔍 Juego 01</a>
        <a href="juego02.php">🎨 Juego 02</a>
        <a href="juego03.php">🏆 Juego 03</a>
        <a href="puntuaciones.php" class="activo">🥇 Puntuaciones</a>
    </div>

</div>

<script>
    // Filtrar tabla por clase
    function filtrarTabla() {
        const filtro = document.getElementById('filtroClase').value.toLowerCase();
        document.querySelectorAll('#tablaRanking tbody tr').forEach(fila => {
            const clase = fila.dataset.clase.toLowerCase();
            fila.style.display = (!filtro || clase === filtro) ? '' : 'none';
        });
    }
</script>

</body>
</html>