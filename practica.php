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
        /* Estilos extra solo para la práctica (se pueden unificar después) */
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
            transition: 0.3s;
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
            background: <?= $data['colorBoton'] ?? '#ffa726' ?>;
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
            max-width: 200px;
            text-align: center;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .avatar {
            font-size: 3rem;
        }
        #mensaje-personaje {
            background: #ffefc0;
            border-radius: 20px;
            padding: 8px;
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
</div>

<div class="personaje-guia" id="personajeGuia">
    <div class="avatar">🧠🐝</div>
    <div id="mensaje-personaje">¡Hola! Lee con atención.</div>
</div>

<script>
// Pasamos los datos de PHP a JavaScript
const temaData = <?= json_encode($data) ?>;
let preguntas = temaData.preguntas;
let indice = 0;
let aciertos = 0;
let bloqueado = false;      // espera a que se presione "siguiente"
let preguntaActual = null;

function actualizarEstrellas() {
    let estrellas = "";
    for (let i = 0; i < aciertos; i++) estrellas += "⭐";
    for (let i = aciertos; i < 5; i++) estrellas += "☆";
    document.getElementById("estrellasDiv").innerText = estrellas;
}

function mostrarPregunta() {
    if (indice >= preguntas.length) {
        // fin del juego
        let mensajeFinal = `🎉 ¡Misión cumplida! 🎉<br>Acertaste ${aciertos} de ${preguntas.length}.<br>${aciertos === preguntas.length ? "🏆 ¡Perfecto! 🏆" : "¡Sigue practicando!"}`;
        document.getElementById("preguntaTexto").innerHTML = mensajeFinal;
        document.getElementById("opcionesDiv").innerHTML = "";
        document.getElementById("feedbackDiv").innerHTML = `<a href='index.php' class='btn-siguiente' style='display:inline-block; text-decoration:none;'>🏠 Volver al menú</a>`;
        document.getElementById("btnSiguiente").style.display = "none";
        return;
    }

    preguntaActual = preguntas[indice];
    document.getElementById("preguntaTexto").innerText = preguntaActual.texto;
    let opcionesHtml = "";
    preguntaActual.opciones.forEach((opt, idx) => {
        opcionesHtml += `<button class="opcion" data-opc="${idx}">${opt}</button>`;
    });
    document.getElementById("opcionesDiv").innerHTML = opcionesHtml;
    document.getElementById("feedbackDiv").innerHTML = "";
    document.getElementById("btnSiguiente").style.display = "none";
    bloqueado = false;

    // Agregar evento a cada opción
    document.querySelectorAll(".opcion").forEach(btn => {
        btn.addEventListener("click", () => responder(parseInt(btn.dataset.opc)));
    });

    // Mensaje de ánimo del personaje
    cambiarMensajePersonaje("🎤 Lee bien y elige la respuesta 💪");
}

function responder(opcElegida) {
    if (bloqueado) return;
    const esCorrecta = (opcElegida === preguntaActual.correcta);
    bloqueado = true;

    let feedbackHtml = "";
    if (esCorrecta) {
        aciertos++;
        actualizarEstrellas();
        feedbackHtml = `<span style="color:green; font-size:2rem;">✅ ¡Correcto!</span><br>${preguntaActual.explicacion}`;
        cambiarMensajePersonaje("🎉 ¡Excelente! +1 estrella 🎉");
        // Sonido opcional si se quiere (podemos simular con vibración o solo visual)
    } else {
        const correctaTexto = preguntaActual.opciones[preguntaActual.correcta];
        feedbackHtml = `<span style="color:red; font-size:2rem;">❌ Casi...</span><br>La respuesta correcta es: <strong>${correctaTexto}</strong><br>${preguntaActual.explicacion}`;
        cambiarMensajePersonaje("😅 ¡Ánimo! Revisa por qué era esa respuesta.");
    }
    if (preguntaActual.datoDivertido) {
        feedbackHtml += `<div class="dato-div">🎲 Dato curioso: ${preguntaActual.datoDivertido}</div>`;
    }
    document.getElementById("feedbackDiv").innerHTML = feedbackHtml;
    document.getElementById("btnSiguiente").style.display = "block";
}

function cambiarMensajePersonaje(mensaje) {
    let msgDiv = document.getElementById("mensaje-personaje");
    if (msgDiv) {
        msgDiv.innerText = mensaje;
        // Animación leve
        msgDiv.style.transform = "scale(1.05)";
        setTimeout(() => { msgDiv.style.transform = "scale(1)"; }, 300);
    }
}

document.getElementById("btnSiguiente").addEventListener("click", () => {
    indice++;
    mostrarPregunta();
});

// Iniciar
mostrarPregunta();
actualizarEstrellas();

// Agregar síntesis de voz opcional (solo si el navegador lo soporta)
if ('speechSynthesis' in window) {
    let botonVoz = document.createElement("button");
    botonVoz.innerText = "🔊 Leer pregunta";
    botonVoz.style.background = "#ffab40";
    botonVoz.style.border = "none";
    botonVoz.style.borderRadius = "30px";
    botonVoz.style.padding = "8px 20px";
    botonVoz.style.fontSize = "1.2rem";
    botonVoz.style.cursor = "pointer";
    botonVoz.style.marginBottom = "15px";
    botonVoz.onclick = () => {
        let texto = document.getElementById("preguntaTexto").innerText;
        let utterance = new SpeechSynthesisUtterance(texto);
        utterance.lang = 'es-ES';
        speechSynthesis.cancel();
        speechSynthesis.speak(utterance);
    };
    document.querySelector(".contenedor-pregunta").prepend(botonVoz);
}
</script>
</body>
</html>
