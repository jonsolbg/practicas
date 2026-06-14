<?php
$tema = $_GET['tema'] ?? '';
$archivo_json = "data/{$tema}.json";
if (!file_exists($archivo_json)) {
    die("❌ Práctica no encontrada. <a href='index.php'>Volver al menú</a>");
}
$json_content = file_get_contents($archivo_json);
$data = json_decode($json_content, true);
if (!$data) {
    die("❌ Error en el archivo de preguntas.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['titulo'] ?? $tema) ?> 🎯</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        body {
            background: <?= $data['colorFondo'] ?? '#f0f4c3' ?>;
            font-family: 'Comic Neue', 'Segoe UI', cursive;
            text-align: center;
            padding: 20px;
        }
        .contenedor-pregunta {
            background: white;
            border-radius: 60px;
            padding: 30px 20px;
            margin: 20px auto;
            max-width: 800px;
            box-shadow: 0 20px 30px rgba(0,0,0,0.2);
        }
        .pregunta-texto {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 30px;
            color: #1e3c72;
        }
        .opciones {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin: 20px 0;
        }
        .opcion {
            background: <?= $data['colorBoton'] ?? '#ffb74d' ?>;
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
        .btn-siguiente {
            background: #4caf50;
            color: white;
            padding: 12px 30px;
            font-size: 1.8rem;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
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
        }
        .avatar { font-size: 3rem; }
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
        }
    </style>
</head>
<body>

<div class="contenedor-pregunta" id="preguntaContenedor">
    <div class="pregunta-texto" id="preguntaTexto">Cargando...</div>
    <div class="opciones" id="opcionesDiv"></div>
    <div class="estrellas" id="estrellasDiv">☆☆☆☆☆</div>
    <div id="feedbackDiv" class="feedback"></div>
    <button id="btnSiguiente" class="btn-siguiente" style="display:none;">➡️ Siguiente</button>
</div>

<div class="personaje-guia" id="personajeGuia">
    <div class="avatar">🧠🐝</div>
    <div id="mensaje-personaje">¡Hola! Lee con atención.</div>
</div>

<script>
    // Pasamos los datos del tema a JavaScript
    const temaData = <?= json_encode($data) ?>;
</script>
<script src="js/script.js"></script>
<script src="js/personaje.js"></script>
<script src="js/voz.js"></script>
</body>
</html>