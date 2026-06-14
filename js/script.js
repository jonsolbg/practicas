// script.js - Lógica principal de la práctica
let preguntas = temaData.preguntas;
let indice = 0;
let aciertos = 0;
let bloqueado = false;
let preguntaActual = null;

function actualizarEstrellas() {
    let estrellas = "";
    for (let i = 0; i < aciertos; i++) estrellas += "⭐";
    for (let i = aciertos; i < 5; i++) estrellas += "☆";
    document.getElementById("estrellasDiv").innerText = estrellas;
}

function mostrarPregunta() {
    if (indice >= preguntas.length) {
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

    document.querySelectorAll(".opcion").forEach(btn => {
        btn.addEventListener("click", () => responder(parseInt(btn.dataset.opc)));
    });

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

document.getElementById("btnSiguiente").addEventListener("click", () => {
    indice++;
    mostrarPregunta();
});

mostrarPregunta();
actualizarEstrellas();
