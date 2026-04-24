<?php
/**
 * ============================================================
 *  MASTER ARRAY - Configuración Central del Proyecto
 * ============================================================
 *  Este archivo es el ÚNICO que necesitas modificar para
 *  adaptar el juego a tu clase o tema bíblico.
 *
 *  Contiene 4 secciones:
 *   1. Nombres de los participantes
 *   2. Palabras para la Sopa de Letras (Juego 01)
 *   3. Imágenes para Pintura Mágica   (Juego 02)
 *   4. Preguntas y respuestas          (Juego 03)
 *
 *  ⚠️  IMPORTANTE: Las claves (ej: "ADAN Y EVA") deben ser
 *      IDÉNTICAS en las 4 secciones para que el sistema
 *      funcione correctamente.
 * ============================================================
 */

// ============================================================
// SECCIÓN 1 — NOMBRES DE LOS PARTICIPANTES
// ============================================================
// Agrega aquí el nombre completo de cada niño/a en MAYÚSCULAS.
// El sistema usará esta lista para el login de cada juego.
// ⚠️  No uses tildes ni caracteres especiales en los nombres.
// ============================================================
$nombres_permitidos = [
    "NOMBRE 01",    // 👈 Reemplaza con el nombre real del participante
    "NOMBRE 02",    // 👈 Puedes agregar tantos como necesites
    // "NOMBRE 03", // 👈 Descomenta esta línea para agregar más
];


// ============================================================
// SECCIÓN 2 — SOPA DE LETRAS (Juego 01)
// ============================================================
// Cada clave es el nombre del tema (ej: "ADAN Y EVA").
// El valor es un array con exactamente 10 palabras en MAYÚSCULAS
// que aparecerán ocultas en la sopa de letras.
// ⚠️  Usa solo letras simples, sin tildes ni espacios.
// ============================================================
$clases = [
    "ADAN Y EVA" => [
        "GENESIS",      // Palabra 1
        "TIERRA",       // Palabra 2
        "VIVIENTE",     // Palabra 3
        "SUENO",        // Palabra 4  (sin tilde: SUEÑO → SUENO)
        "FRUTO",        // Palabra 5
        "TUNICAS",      // Palabra 6
        "PROMESA",      // Palabra 7
        "PECADO",       // Palabra 8
        "REDENTOR",     // Palabra 9
        "LONGEVIDAD"    // Palabra 10
    ],
    // ── Para agregar un nuevo tema, copia el bloque de arriba ──
    // "NOEMBARCO" => ["NOE", "DILUVIO", "PALOMA", ...],
];


// ============================================================
// SECCIÓN 3 — IMAGEN PARA PINTAR (Juego 02)
// ============================================================
// Cada clave debe coincidir con la clave de $clases.
// El valor es la ruta a la imagen que los niños colorearán.
// ⚠️  Coloca tus imágenes en la carpeta /img/ del proyecto.
//     Se recomienda PNG con fondo blanco y líneas negras.
// ============================================================
$dibujos_clases = [
    "ADAN Y EVA" => "img/adanyeva.png",
    // "NOEMBARCO" => "img/noe.png",   // 👈 Agrega la imagen de tu nuevo tema
];


// ============================================================
// SECCIÓN 4 — PREGUNTAS Y RESPUESTAS (Juego 03)
// ============================================================
// Cada clave debe coincidir con la clave de $clases.
// Cada pregunta tiene:
//   "q"       → El texto de la pregunta
//   "options" → Array con exactamente 3 opciones (A, B, C)
//   "correct" → Índice de la respuesta correcta (0=A, 1=B, 2=C)
// ⚠️  Se recomienda un mínimo de 10 preguntas por tema.
// ============================================================
$banco_preguntas = [
    "ADAN Y EVA" => [

        // ── Pregunta 1 ──────────────────────────────────────
        [
            "q"       => "¿Qué significa el nombre 'Eva' (Hawwāh)?",
            "options" => ["A) La elegida", "B) Viviente o la que da vida", "C) Compañera"],
            "correct" => 1   // ✅ Respuesta correcta: B
        ],

        // ── Pregunta 2 ──────────────────────────────────────
        [
            "q"       => "¿Qué método usó Dios para formar a la mujer?",
            "options" => ["A) Un sueño profundo (primera anestesia)", "B) El mismo polvo de la tierra", "C) Agua y espíritu"],
            "correct" => 0   // ✅ Respuesta correcta: A
        ],

        // ── Pregunta 3 ──────────────────────────────────────
        [
            "q"       => "¿Cuál era la dieta original en el Edén?",
            "options" => ["A) Carne y verduras", "B) Solo legumbres", "C) Toda planta que da semilla y todo árbol con fruto"],
            "correct" => 2   // ✅ Respuesta correcta: C
        ],

        // ── Pregunta 4 ──────────────────────────────────────
        [
            "q"       => "¿Cuándo se permitió el consumo de carne según el texto?",
            "options" => ["A) Inmediatamente después del pecado", "B) Después del diluvio", "C) Cuando salieron del Edén"],
            "correct" => 1   // ✅ Respuesta correcta: B
        ],

        // ── Pregunta 5 ──────────────────────────────────────
        [
            "q"       => "¿Qué tarea realizó Adán antes de la creación de Eva?",
            "options" => ["A) Construir un refugio", "B) Nombrar a los animales", "C) Cultivar el huerto"],
            "correct" => 1   // ✅ Respuesta correcta: B
        ],

        // ── Pregunta 6 ──────────────────────────────────────
        [
            "q"       => "¿De qué material fueron las túnicas que Dios hizo para vestirlos?",
            "options" => ["A) Hojas de higuera", "B) Pieles (implicando un sacrificio)", "C) Lana de oveja"],
            "correct" => 1   // ✅ Respuesta correcta: B
        ],

        // ── Pregunta 7 ──────────────────────────────────────
        [
            "q"       => "¿A quién de los descendientes de Noé llegó a conocer Adán?",
            "options" => ["A) Lamec (padre de Noé)", "B) Matusalén", "C) Sem"],
            "correct" => 0   // ✅ Respuesta correcta: A
        ],

        // ── Pregunta 8 ──────────────────────────────────────
        [
            "q"       => "¿Qué dice la Biblia sobre el tipo de fruto prohibido?",
            "options" => ["A) Era una manzana roja", "B) Solo lo llama 'fruto del árbol de la ciencia del bien y del mal'", "C) Era un higo"],
            "correct" => 1   // ✅ Respuesta correcta: B
        ],

        // ── Pregunta 9 ──────────────────────────────────────
        [
            "q"       => "¿Qué es el 'Protoevangelio' mencionado en la maldición a la serpiente?",
            "options" => ["A) El castigo eterno", "B) La promesa de que la simiente de la mujer heriría al enemigo", "C) El fin del mundo"],
            "correct" => 1   // ✅ Respuesta correcta: B
        ],

        // ── Pregunta 10 ─────────────────────────────────────
        [
            "q"       => "¿Por qué se responsabiliza a Adán de la entrada del pecado?",
            "options" => ["A) Porque él comió más", "B) Porque él recibió el mandato directamente de Dios", "C) Porque él era el mayor"],
            "correct" => 1   // ✅ Respuesta correcta: B
        ],

        // ── Agrega más preguntas aquí copiando el bloque ────
    ],

    // ── Para agregar un nuevo tema, copia el bloque completo ──
    // "NOEMBARCO" => [ ["q" => "...", "options" => [...], "correct" => 0], ],
];

?>