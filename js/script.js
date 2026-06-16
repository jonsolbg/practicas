// ============================================
// script.js - Lógica principal de la práctica
// Soporta: Múltiple, Verdadero/Falso, Asociación
// ============================================

// ============================================
// VARIABLES GLOBALES
// ============================================
let preguntas = [];
let indice = 0;
let aciertos = 0;
let aciertosSeguidos = 0;
let bloqueado = false;
let preguntaActual = null;

// Configuración
let configRacha = null;
let sonidosActivados = false;
let volumenSonidos = 0.7;

// ============================================
// DETECCIÓN DE TIPO (retrocompatibilidad)
// ============================================
function obtenerTipoPregunta(pregunta) {
    if (pregunta.tipo) return pregunta.tipo;
    if (pregunta.opciones && Array.isArray(pregunta.opciones)) return 'multiple';
    if (pregunta.pares && Array.isArray(pregunta.pares)) return 'asociar';
    return 'multiple';
}

// ============================================
// SONIDOS
// ============================================
function cargarConfigSonidos() {
    if (typeof configSonidos !== 'undefined' && configSonidos) {
        sonidosActivados = configSonidos.activo === true;
        volumenSonidos = configSonidos.volumen || 0.7;
        console.log("🔊 Sonidos activados:", sonidosActivados);
    }
}

function reproducirSonido(id) {
    if (!sonidosActivados) return;
    let audio = document.getElementById(id);
    if (audio) {
        audio.volume = volumenSonidos;
        audio.currentTime = 0;
        audio.play().catch(e => console.log('Error reproduciendo sonido:', e));
    }
}

// ============================================
// ESTRELLAS Y RACHA
// ============================================
function actualizarEstrellas() {
    let estrellas = "";
    let estrellasMostrar = Math.min(aciertos, 5);
    for (let i = 0; i < estrellasMostrar; i++) estrellas += "⭐";
    for (let i = estrellasMostrar; i < 5; i++) estrellas += "☆";
    
    let extra = aciertos > 5 ? ` +${aciertos - 5}` : "";
    document.getElementById("estrellasDiv").innerHTML = estrellas + `<span style="font-size: 1rem;">${extra}</span>`;
    
    if (configRacha && configRacha.activo && aciertosSeguidos >= (configRacha.niveles && configRacha.niveles[0] ? configRacha.niveles[0].aciertos : 3)) {
        let nivelActual = getNivelRacha();
        if (nivelActual) {
            let rachaHtml = `<div class="indicador-racha" style="color: ${nivelActual.color}; margin-top: 10px; font-size: 1.3rem;">
                                ${nivelActual.emoji} Racha: ${aciertosSeguidos} seguidos ${nivelActual.emoji}
                              </div>`;
            if (!document.getElementById("indicadorRacha")) {
                document.getElementById("estrellasDiv").insertAdjacentHTML('afterend', rachaHtml);
            } else {
                let indicador = document.getElementById("indicadorRacha");
                indicador.innerHTML = `${nivelActual.emoji} Racha: ${aciertosSeguidos} seguidos ${nivelActual.emoji}`;
                indicador.style.color = nivelActual.color;
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

// ============================================
// ANIMACIONES DE RACHA
// ============================================
function mostrarAnimacionRacha() {
    let nivel = getNivelRacha();
    if (!nivel) return;
    
    reproducirSonido('sonidoRacha');
    
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
    
    if (nivel.animacion === 'flameante') {
        document.body.style.transition = 'background 0.3s';
        document.body.style.backgroundColor = '#fff3e0';
        setTimeout(() => { document.body.style.backgroundColor = ''; }, 500);
    } else if (nivel.animacion === 'confeti') {
        crearConfeti();
    } else if (nivel.animacion === 'estrellas') {
        crearEstrellasVoladoras();
    }
    
    setTimeout(() => { animDiv.remove(); }, 1500);
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

// Agregar CSS de animaciones si no existe
if (!document.getElementById('animaciones-css')) {
    let style = document.createElement('style');
    style.id = 'animaciones-css';
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
        .opcion-seleccionada {
            background: #ff9800 !important;
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(255,152,0,0.5);
        }
        .opcion-correcta {
            background: #4caf50 !important;
            transform: scale(1.02);
        }
        .opcion-incorrecta {
            background: #f44336 !important;
            transform: scale(0.98);
        }
    `;
    document.head.appendChild(style);
}

// ============================================
// LIMPIAR ESTILOS DE BOTONES
// ============================================
function limpiarEstilosBotones() {
    let botones = document.querySelectorAll(".opcion, .vf-btn");
    for (let i = 0; i < botones.length; i++) {
        botones[i].classList.remove("opcion-seleccionada", "opcion-correcta", "opcion-incorrecta");
    }
}

// ============================================
// MANEJAR RESPUESTA (Múltiple y V/F)
// ============================================
function manejarRespuesta(tipo, valor) {
    if (bloqueado) return;
    
    let esCorrecta = false;
    let respuestaCorrectaTexto = "";
    
    if (tipo === 'multiple') {
        esCorrecta = (parseInt(valor) === preguntaActual.correcta);
        respuestaCorrectaTexto = preguntaActual.opciones[preguntaActual.correcta];
    } else if (tipo === 'vf') {
        esCorrecta = (valor === 'true') === preguntaActual.correcta;
        respuestaCorrectaTexto = preguntaActual.correcta ? 'Verdadero' : 'Falso';
    }
    
    bloqueado = true;
    
    // Marcar visualmente los botones
    let botones = document.querySelectorAll(".opcion, .vf-btn");
    for (let i = 0; i < botones.length; i++) {
        let btn = botones[i];
        if (tipo === 'multiple') {
            if (parseInt(btn.dataset.valor) === preguntaActual.correcta) {
                btn.classList.add("opcion-correcta");
            }
            if (btn === document.querySelector(`.opcion[data-valor="${valor}"]`) && !esCorrecta) {
                btn.classList.add("opcion-incorrecta");
            }
        } else if (tipo === 'vf') {
            if ((btn.dataset.valor === 'true' && preguntaActual.correcta === true) ||
                (btn.dataset.valor === 'false' && preguntaActual.correcta === false)) {
                btn.classList.add("opcion-correcta");
            }
            if (btn.dataset.valor === valor && !esCorrecta) {
                btn.classList.add("opcion-incorrecta");
            }
        }
        btn.disabled = true;
    }
    
    let feedbackHtml = "";
    
    if (esCorrecta) {
        aciertos++;
        aciertosSeguidos++;
        actualizarEstrellas();
        reproducirSonido('sonidoCorrecto');
        feedbackHtml = `<span style="color:green; font-size:2rem;">✅ ¡Correcto!</span><br>${preguntaActual.explicacion}`;
        
        let nivelAlcanzado = getNivelRacha();
        if (nivelAlcanzado && aciertosSeguidos >= nivelAlcanzado.aciertos && (aciertosSeguidos - 1) < nivelAlcanzado.aciertos) {
            mostrarAnimacionRacha();
        }
        
        if (typeof cambiarMensajePersonaje !== 'undefined') {
            if (aciertosSeguidos >= 5) {
                cambiarMensajePersonaje("🔥 ¡Vas volando! " + aciertosSeguidos + " seguidas 🔥");
            } else {
                cambiarMensajePersonaje("🎉 ¡Excelente! +1 estrella 🎉");
            }
        }
    } else {
        if (configRacha && configRacha.resetearAlFallar && aciertosSeguidos > 0) {
            let rachaPerdida = aciertosSeguidos;
            aciertosSeguidos = 0;
            actualizarEstrellas();
            feedbackHtml = `<span style="color:red; font-size:2rem;">❌ Incorrecto</span><br>La respuesta correcta es: <strong>${respuestaCorrectaTexto}</strong><br>${preguntaActual.explicacion}<div style="color:orange; margin-top:10px;">⚠️ ¡Se acabó la racha de ${rachaPerdida}! Pero puedes empezar otra ⚠️</div>`;
        } else {
            aciertosSeguidos = 0;
            actualizarEstrellas();
            feedbackHtml = `<span style="color:red; font-size:2rem;">❌ Incorrecto</span><br>La respuesta correcta es: <strong>${respuestaCorrectaTexto}</strong><br>${preguntaActual.explicacion}<div style="color:#ff9800; margin-top:10px;">💪 ¡Sigue intentando! La próxima será la buena 💪</div>`;
        }
        reproducirSonido('sonidoIncorrecto');
        
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

// ============================================
// CONFIGURAR EVENTOS DE RESPUESTA
// ============================================
function configurarEventosRespuesta() {
    let botones = document.querySelectorAll(".opcion");
    for (let i = 0; i < botones.length; i++) {
        let btn = botones[i];
        let tipo = btn.dataset.tipo || 'multiple';
        let valor = btn.dataset.valor !== undefined ? btn.dataset.valor : btn.dataset.opc;
        
        // Remover event listeners antiguos para evitar duplicados
        let nuevoBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(nuevoBtn, btn);
        
        nuevoBtn.addEventListener('click', function() {
            if (tipo === 'asociar-item' || tipo === 'asociar-coincidencia') return;
            manejarRespuesta(tipo, valor);
        });
    }
}

// ============================================
// MOSTRAR PREGUNTA ACTUAL
// ============================================
function mostrarPregunta() {
    // Verificar si ya se terminaron las preguntas
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
        
        const preguntaTexto = document.getElementById("preguntaTexto");
        const opcionesDiv = document.getElementById("opcionesDiv");
        const feedbackDiv = document.getElementById("feedbackDiv");
        const btnSiguiente = document.getElementById("btnSiguiente");
        const btnReiniciar = document.getElementById("btnReiniciar");
        
        if (preguntaTexto) preguntaTexto.innerHTML = mensajeFinal;
        if (opcionesDiv) opcionesDiv.innerHTML = "";
        if (feedbackDiv) feedbackDiv.innerHTML = `<a href='index.php' class='btn-siguiente' style='display:inline-block; text-decoration:none;'>🏠 Volver al menú</a>`;
        if (btnSiguiente) btnSiguiente.style.display = "none";
        if (btnReiniciar) btnReiniciar.style.display = "inline-block";
        
        guardarProgreso(aciertos, preguntas.length, aciertosSeguidos, temaData.titulo);
        return;
    }
    
    // Obtener la pregunta actual
    preguntaActual = preguntas[indice];
    let tipo = obtenerTipoPregunta(preguntaActual);
    
    // 🔴 IMPORTANTE: Limpiar el contenedor de preguntas
    const opcionesDiv = document.getElementById("opcionesDiv");
    const preguntaTextoDiv = document.getElementById("preguntaTexto");
    
    // Para preguntas de asociación, el texto va DENTRO del renderizado
    // Para múltiple y V/F, el texto va en el elemento separado
    if (tipo === 'asociar') {
        // Asociación: el texto ya está dentro de renderizarAsociar
        if (preguntaTextoDiv) preguntaTextoDiv.innerHTML = "";
    } else {
        // Múltiple o V/F: mostrar texto arriba
        if (preguntaTextoDiv) preguntaTextoDiv.innerHTML = preguntaActual.texto;
    }
    
    // Renderizar según el tipo
    if (typeof renderizarPregunta === 'function') {
        let html = renderizarPregunta(preguntaActual, indice);
        if (opcionesDiv) {
            opcionesDiv.innerHTML = html;
        }
        
        // Si es asociación, configurar eventos especiales
        if (tipo === 'asociar' && typeof configurarEventosAsociar === 'function') {
            setTimeout(() => {
                configurarEventosAsociar(preguntaActual, indice);
            }, 100);
        } else {
            setTimeout(() => {
                configurarEventosRespuesta();
            }, 50);
        }
    } else {
        // Fallback para múltiple (si no existe tipos_preguntas.js)
        if (opcionesDiv && preguntaActual.opciones) {
            let opcionesHtml = '<div class="opciones">';
            for (let i = 0; i < preguntaActual.opciones.length; i++) {
                opcionesHtml += `<button class="opcion" data-tipo="multiple" data-valor="${i}">${preguntaActual.opciones[i]}</button>`;
            }
            opcionesHtml += '</div>';
            opcionesDiv.innerHTML = opcionesHtml;
            setTimeout(() => {
                configurarEventosRespuesta();
            }, 50);
        }
    }
    
    // Limpiar feedback y ocultar botón siguiente
    const feedbackDiv = document.getElementById("feedbackDiv");
    const btnSiguiente = document.getElementById("btnSiguiente");
    if (feedbackDiv) feedbackDiv.innerHTML = "";
    if (btnSiguiente) btnSiguiente.style.display = "none";
    
    bloqueado = false;
    
    if (typeof cambiarMensajePersonaje !== 'undefined') {
        cambiarMensajePersonaje("🎤 Lee bien y elige la respuesta 💪");
    }
}

// ============================================
// SIGUIENTE PREGUNTA
// ============================================
document.getElementById("btnSiguiente").addEventListener("click", function() {
    indice++;
    // Limpiar el contenedor visualmente antes de cargar la siguiente
    const opcionesDiv = document.getElementById("opcionesDiv");
    if (opcionesDiv) {
        opcionesDiv.innerHTML = '<div style="text-align:center; padding:40px;">⏳ Cargando siguiente pregunta...</div>';
    }
    // Pequeño retraso para dar tiempo a que el DOM se actualice
    setTimeout(() => {
        mostrarPregunta();
    }, 50);
});

// ============================================
// GUARDAR PROGRESO EN EL SERVIDOR
// ============================================
function guardarProgreso(aciertos, total, rachaMaxima, temaNombre) {
    let urlParams = new URLSearchParams(window.location.search);
    let temaCompleto = urlParams.get('tema');
    
    let categoria = "";
    let archivo = "";
    if (temaCompleto) {
        let partes = temaCompleto.split('/');
        if (partes.length === 2) {
            categoria = partes[0];
            archivo = partes[1];
        } else {
            categoria = "general";
            archivo = temaCompleto;
        }
    }
    
    let datos = {
        fecha: new Date().toISOString(),
        tema_categoria: categoria,
        tema_archivo: archivo,
        tema_nombre: temaNombre,
        total_preguntas: total,
        aciertos: aciertos,
        racha_maxima: rachaMaxima
    };
    
    fetch('guardar_progreso.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
    }).catch(error => console.log('Error guardando progreso:', error));
}

// ============================================
// INICIALIZACIÓN
// ============================================
function inicializar() {
    // Cargar preguntas
    if (typeof temaData !== 'undefined' && temaData.preguntas) {
        preguntas = temaData.preguntas;
    }
    
    // Cargar configuración de racha
    if (typeof temaData.racha !== 'undefined') {
        configRacha = temaData.racha;
    } else if (typeof configRachaGlobal !== 'undefined') {
        configRacha = configRachaGlobal;
    }
    
    // Cargar configuración de sonidos
    cargarConfigSonidos();
    
    // Iniciar juego
    actualizarEstrellas();
    mostrarPregunta();
}

// Iniciar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializar);
} else {
    inicializar();
}