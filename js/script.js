// script.js - Lógica principal de la práctica con sistema de racha

// Variables globales
let preguntas = temaData.preguntas;
let indice = 0;
let aciertos = 0;
let aciertosSeguidos = 0;  // ← NUEVO: contador de racha
let bloqueado = false;
let preguntaActual = null;

// Cargar configuración de racha (desde PHP, la pasamos al final)
let configRacha = typeof temaData.racha !== 'undefined' ? temaData.racha : null;

function actualizarEstrellas() {
    let estrellas = "";
    // Estrellas por aciertos totales (máximo 5 visibles, pero cuenta todos)
    let estrellasMostrar = Math.min(aciertos, 5);
    for (let i = 0; i < estrellasMostrar; i++) estrellas += "⭐";
    for (let i = estrellasMostrar; i < 5; i++) estrellas += "☆";
    
    // Si hay más de 5 aciertos, mostrar +X
    let extra = aciertos > 5 ? ` +${aciertos - 5}` : "";
    document.getElementById("estrellasDiv").innerHTML = estrellas + `<span style="font-size: 1rem;">${extra}</span>`;
    
    // Mostrar racha actual (si está activa)
    if (configRacha && configRacha.activo && aciertosSeguidos >= (configRacha.niveles[0]?.aciertos || 3)) {
        let nivelActual = getNivelRacha();
        if (nivelActual) {
            let rachaHtml = `<div id="indicadorRacha" style="margin-top: 10px; font-size: 1.3rem; color: ${nivelActual.color};">
                                ${nivelActual.emoji} Racha: ${aciertosSeguidos} seguidos ${nivelActual.emoji}
                              </div>`;
            if (!document.getElementById("indicadorRacha")) {
                document.getElementById("estrellasDiv").insertAdjacentHTML('afterend', rachaHtml);
            } else {
                document.getElementById("indicadorRacha").innerHTML = `${nivelActual.emoji} Racha: ${aciertosSeguidos} seguidos ${nivelActual.emoji}`;
                document.getElementById("indicadorRacha").style.color = nivelActual.color;
            }
        }
    } else {
        let indicador = document.getElementById("indicadorRacha");
        if (indicador) indicador.remove();
    }
}

function getNivelRacha() {
    if (!configRacha || !configRacha.niveles) return null;
    let niveles = configRacha.niveles;
    let nivelActual = null;
    for (let i = 0; i < niveles.length; i++) {
        if (aciertosSeguidos >= niveles[i].aciertos) {
            nivelActual = niveles[i];
        }
    }
    return nivelActual;
}

function mostrarAnimacionRacha() {
    let nivel = getNivelRacha();
    if (!nivel) return;
    
    // Crear elemento flotante animado
    let animDiv = document.createElement("div");
    animDiv.className = "animacion-racha";
    animDiv.innerHTML = `
        <div style="
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: ${nivel.color};
            color: white;
            padding: 20px 40px;
            border-radius: 60px;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            z-index: 1000;
            animation: aparecerYDesaparecer 1.5s ease-out forwards;
            box-shadow: 0 0 30px ${nivel.color};
        ">
            ${nivel.emoji} ${nivel.mensaje} ${nivel.emoji}
        </div>
    `;
    document.body.appendChild(animDiv);
    
    // Efectos adicionales según el tipo de animación
    if (nivel.animacion === 'flameante') {
        document.body.style.transition = 'background 0.3s';
        document.body.style.backgroundColor = '#fff3e0';
        setTimeout(() => { document.body.style.backgroundColor = ''; }, 500);
    } else if (nivel.animacion === 'confeti') {
        crearConfeti();
    } else if (nivel.animacion === 'estrellas') {
        crearEstrellasVoladoras();
    }
    
    setTimeout(() => {
        animDiv.remove();
    }, 1500);
}

function crearConfeti() {
    for (let i = 0; i < 50; i++) {
        let confeti = document.createElement("div");
        confeti.innerHTML = ["🎉", "✨", "⭐", "🌟", "🎊"][Math.floor(Math.random() * 5)];
        confeti.style.position = "fixed";
        confeti.style.left = Math.random() * 100 + "%";
        confeti.style.top = "-30px";
        confeti.style.fontSize = (20 + Math.random() * 20) + "px";
        confeti.style.pointerEvents = "none";
        confeti.style.zIndex = "999";
        confeti.style.animation = `caer ${1 + Math.random() * 2}s linear forwards`;
        document.body.appendChild(confeti);
        setTimeout(() => confeti.remove(), 3000);
    }
}

function crearEstrellasVoladoras() {
    for (let i = 0; i < 20; i++) {
        let estrella = document.createElement("div");
        estrella.innerHTML = ["⭐", "🌟", "✨"][Math.floor(Math.random() * 3)];
        estrella.style.position = "fixed";
        estrella.style.left = Math.random() * 100 + "%";
        estrella.style.bottom = "0px";
        estrella.style.fontSize = (15 + Math.random() * 25) + "px";
        estrella.style.pointerEvents = "none";
        estrella.style.zIndex = "999";
        estrella.style.animation = `subir ${1 + Math.random() * 2}s ease-out forwards`;
        document.body.appendChild(estrella);
        setTimeout(() => estrella.remove(), 2000);
    }
}

// Agregar CSS para las animaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes aparecerYDesaparecer {
        0% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
        20% { opacity: 1; transform: translate(-50%, -50%) scale(1.1); }
        80% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        100% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
    }
    @keyframes caer {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
    }
    @keyframes subir {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
    }
`;
document.head.appendChild(style);

function mostrarPregunta() {
    if (indice >= preguntas.length) {
        let mensajeFinal = `🎉 ¡Misión cumplida! 🎉<br>Acertaste ${aciertos} de ${preguntas.length}.<br>`;
        if (aciertos === preguntas.length) {
            mensajeFinal += "🏆 ¡Perfecto! 🏆<br>¡Eres un campeón!";
            if (aciertosSeguidos >= 5) {
                mensajeFinal += `<br><span style="font-size:1.5rem;">🔥 Racha final: ${aciertosSeguidos} seguidos 🔥</span>`;
            }
        } else {
            mensajeFinal += "¡Sigue practicando! 💪";
        }
        document.getElementById("preguntaTexto").innerHTML = mensajeFinal;
        document.getElementById("opcionesDiv").innerHTML = "";
        document.getElementById("feedbackDiv").innerHTML = `<a href='index.php' class='btn-siguiente' style='display:inline-block; text-decoration:none;'>🏠 Volver al menú</a>`;
        document.getElementById("btnSiguiente").style.display = "none";
        return;
    }

    preguntaActual = preguntas[indice];
    document.getElementById("preguntaTexto").innerText = preguntaActual.texto;
    
    let opcionesHtml = "";
    for (let i = 0; i < preguntaActual.opciones.length; i++) {
        opcionesHtml += `<button class="opcion" data-opc="${i}">${preguntaActual.opciones[i]}</button>`;
    }
    document.getElementById("opcionesDiv").innerHTML = opcionesHtml;
    document.getElementById("feedbackDiv").innerHTML = "";
    document.getElementById("btnSiguiente").style.display = "none";
    bloqueado = false;

    let botones = document.querySelectorAll(".opcion");
    for (let i = 0; i < botones.length; i++) {
        botones[i].addEventListener("click", (function(idx) {
            return function() { responder(parseInt(idx)); };
        })(botones[i].dataset.opc));
    }

    if (typeof cambiarMensajePersonaje !== 'undefined') {
        cambiarMensajePersonaje("🎤 Lee bien y elige la respuesta 💪");
    }
}

function responder(opcElegida) {
    if (bloqueado) return;
    let esCorrecta = (opcElegida === preguntaActual.correcta);
    bloqueado = true;

    let feedbackHtml = "";
    let rachaAntes = aciertosSeguidos;
    
    if (esCorrecta) {
        aciertos++;
        aciertosSeguidos++;
        actualizarEstrellas();
        feedbackHtml = `<span style="color:green; font-size:2rem;">✅ ¡Correcto!</span><br>${preguntaActual.explicacion}`;
        
        // Verificar si alcanzó un nuevo nivel de racha
        let nivelAlcanzado = getNivelRacha();
        if (nivelAlcanzado && rachaAntes < nivelAlcanzado.aciertos && aciertosSeguidos >= nivelAlcanzado.aciertos) {
            mostrarAnimacionRacha();
        } else if (aciertosSeguidos >= 3) {
            // Mostrar indicador de racha actualizado
            actualizarEstrellas();
        }
        
        if (typeof cambiarMensajePersonaje !== 'undefined') {
            if (aciertosSeguidos >= 5) {
                cambiarMensajePersonaje("🔥 ¡Vas volando! " + aciertosSeguidos + " seguidas 🔥");
            } else {
                cambiarMensajePersonaje("🎉 ¡Excelente! +1 estrella 🎉");
            }
        }
    } else {
        let correctaTexto = preguntaActual.opciones[preguntaActual.correcta];
        feedbackHtml = `<span style="color:red; font-size:2rem;">❌ Casi...</span><br>La respuesta correcta es: <strong>${correctaTexto}</strong><br>${preguntaActual.explicacion}`;
        
        // Si está configurado para resetear la racha al fallar
        if (configRacha && configRacha.resetearAlFallar && aciertosSeguidos > 0) {
            let rachaPerdida = aciertosSeguidos;
            aciertosSeguidos = 0;
            actualizarEstrellas();
            feedbackHtml += `<div style="color:orange; margin-top:10px;">⚠️ ¡Se acabó la racha de ${rachaPerdida}! Pero puedes empezar otra ⚠️</div>`;
        } else {
            // Si no resetea, solo mostrar que falló pero la racha sigue (para no desmotivar)
            feedbackHtml += `<div style="color:#ff9800; margin-top:10px;">💪 ¡Sigue intentando! La racha no se pierde 💪</div>`;
        }
        
        if (typeof cambiarMensajePersonaje !== 'undefined') {
            cambiarMensajePersonaje("😅 ¡Ánimo! Revisa por qué era esa respuesta.");
        }
    }
    
    if (preguntaActual.datoDivertido) {
        feedbackHtml += `<div class="dato-div">🎲 Dato curioso: ${preguntaActual.datoDivertido}</div>`;
    }
    document.getElementById("feedbackDiv").innerHTML = feedbackHtml;
    document.getElementById("btnSiguiente").style.display = "block";
}

document.getElementById("btnSiguiente").addEventListener("click", function() {
    indice++;
    mostrarPregunta();
});

mostrarPregunta();
actualizarEstrellas();