<?php
// ============================================
// CARGAR TEMA Y CONFIGURACIÓN
// ============================================
$tema = isset($_GET['tema']) ? $_GET['tema'] : '';
$archivo_json = "data/{$tema}.json";

if (!file_exists($archivo_json)) {
    die("❌ Práctica no encontrada. <a href='index.php'>Volver al menú</a>");
}

$json_content = file_get_contents($archivo_json);
$data = json_decode($json_content, true);
if (!$data) {
    die("❌ Error en el archivo de preguntas.");
}

// Cargar configuración global (para racha, colores, etc.)
$configGlobal = array();
if (file_exists("config.json")) {
    $configJson = file_get_contents("config.json");
    $configGlobal = json_decode($configJson, true);
}

// Colores del tema (con fallback)
$colorFondo = isset($data['colorFondo']) ? $data['colorFondo'] : (isset($configGlobal['colorFondoPorDefecto']) ? $configGlobal['colorFondoPorDefecto'] : '#f0f4c3');
$colorBoton = isset($data['colorBoton']) ? $data['colorBoton'] : (isset($configGlobal['colorBotonPorDefecto']) ? $configGlobal['colorBotonPorDefecto'] : '#ffb74d');
$titulo = isset($data['titulo']) ? $data['titulo'] : ucfirst(str_replace('_', ' ', basename($tema)));

// Configuración de racha (prioriza la del tema, luego la global)
$rachaConfig = isset($data['racha']) ? $data['racha'] : (isset($configGlobal['racha']) ? $configGlobal['racha'] : array('activo' => false));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?php echo htmlspecialchars($titulo); ?> 🎯</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            background: <?php echo $colorFondo; ?>;
            font-family: 'Comic Neue', 'Segoe UI', 'Comic Neue', 'Chalkboard SE', cursive;
            text-align: center;
            padding: 20px;
            margin: 0;
            min-height: 100vh;
        }
        .contenedor-pregunta {
            background: white;
            border-radius: 60px;
            padding: 30px 20px;
            margin: 20px auto;
            max-width: 800px;
            box-shadow: 0 20px 30px rgba(0,0,0,0.2);
            transition: 0.3s;
        }
        .pregunta-texto {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 30px;
            color: #1e3c72;
            line-height: 1.3;
        }
        .opciones {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin: 20px 0;
        }
        .opcion {
            background: <?php echo $colorBoton; ?>;
            border: none;
            border-radius: 50px;
            padding: 15px 20px;
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 5px 0 rgba(0,0,0,0.2);
        }
        .opcion:hover {
            transform: scale(1.02);
            background: <?php echo $colorBoton; ?>;
            filter: brightness(0.95);
        }
        .opcion:active {
            transform: scale(0.98);
        }
        .feedback {
            font-size: 1.4rem;
            margin: 20px;
            padding: 15px;
            border-radius: 40px;
            background: #e8eaf6;
        }
        .dato-div {
            background: #c8e6c9;
            border-radius: 40px;
            padding: 15px;
            margin-top: 15px;
            font-size: 1.2rem;
        }
        .estrellas {
            font-size: 2.5rem;
            letter-spacing: 10px;
            margin: 20px;
        }
        .indicador-racha {
            margin-top: 10px;
            font-size: 1.3rem;
            font-weight: bold;
            padding: 8px;
            border-radius: 30px;
            display: inline-block;
        }
        .btn-siguiente {
            background: #4caf50;
            color: white;
            padding: 12px 30px;
            font-size: 1.8rem;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.2s;
        }
        .btn-siguiente:hover {
            background: #45a049;
            transform: scale(1.02);
        }
        .personaje-guia {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border-radius: 40px;
            padding: 10px 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            font-size: 1.2rem;
            max-width: 220px;
            text-align: center;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 100;
        }
        .avatar {
            font-size: 3rem;
        }
        #mensaje-personaje {
            background: #ffefc0;
            border-radius: 20px;
            padding: 8px;
        }
        .btn-voz {
            background: #ffab40;
            border: none;
            border-radius: 30px;
            padding: 8px 20px;
            font-size: 1.2rem;
            cursor: pointer;
            margin-bottom: 15px;
            transition: 0.2s;
        }
        .btn-voz:hover {
            background: #ff8f00;
            transform: scale(1.02);
        }
        .btn-reiniciar {
            background: #2196f3;
            color: white;
            border: none;
            border-radius: 30px;
            padding: 8px 20px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 10px;
            margin-left: 10px;
        }
        .btn-reiniciar:hover {
            background: #1976d2;
        }
        @media (max-width: 600px) {
            .pregunta-texto { font-size: 1.5rem !important; }
            .opcion { font-size: 1.2rem !important; padding: 12px 15px !important; }
            .btn-siguiente, .btn-voz { font-size: 1.3rem !important; }
            .personaje-guia { font-size: 0.9rem !important; max-width: 180px; }
            .avatar { font-size: 2rem !important; }
            .estrellas { font-size: 1.8rem; letter-spacing: 5px; }
        }
    </style>
</head>
<body>

<div class="contenedor-pregunta" id="preguntaContenedor">
    <div class="pregunta-texto" id="preguntaTexto">Cargando...</div>
    <div class="opciones" id="opcionesDiv"></div>
    <div class="estrellas" id="estrellasDiv">☆☆☆☆☆</div>
    <div id="feedbackDiv" class="feedback"></div>
    <button id="btnSiguiente" class="btn-siguiente" style="display:none;">➡️ Siguiente pregunta</button>
    <button id="btnReiniciar" class="btn-reiniciar" style="display:none;">🔄 Reiniciar</button>
</div>

<div class="personaje-guia" id="personajeGuia">
    <div class="avatar">🧠🐝</div>
    <div id="mensaje-personaje">¡Hola! Lee con atención.</div>
</div>

<script>
    // Pasamos los datos del tema a JavaScript
    var temaData = <?php echo json_encode($data); ?>;
    
    // Si no hay configuración de racha en el JSON del tema, usar la global
    if (typeof temaData.racha === 'undefined') {
        temaData.racha = <?php echo json_encode($rachaConfig); ?>;
    }
    
    // Guardar el nombre del tema para reiniciar
    var temaActual = "<?php echo htmlspecialchars($tema); ?>";
</script>
<script src="js/script.js"></script>
<script src="js/personaje.js"></script>
<script src="js/voz.js"></script>

<script>
    // Botón de reinicio (recarga la misma página)
    document.getElementById("btnReiniciar").addEventListener("click", function() {
        window.location.href = "practica.php?tema=" + encodeURIComponent(temaActual);
    });
</script>
</body>
</html>