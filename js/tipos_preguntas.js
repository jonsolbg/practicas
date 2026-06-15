// ============================================
// tipos_preguntas.js - Diferentes formatos de preguntas
// Soporta: Múltiple (default), Verdadero/Falso, Asociación
// ============================================

// ============================================
// DETECCIÓN AUTOMÁTICA DE TIPO (retrocompatibilidad)
// ============================================
function obtenerTipoPregunta(pregunta) {
    // Si ya tiene tipo definido, usarlo
    if (pregunta.tipo) {
        return pregunta.tipo;
    }
    // Si tiene opciones y no tiene tipo, es múltiple (default)
    if (pregunta.opciones && Array.isArray(pregunta.opciones)) {
        return 'multiple';
    }
    // Si tiene pares, es asociar
    if (pregunta.pares && Array.isArray(pregunta.pares)) {
        return 'asociar';
    }
    // Por defecto, múltiple
    return 'multiple';
}

// ============================================
// RENDERIZAR SEGÚN TIPO
// ============================================
function renderizarPregunta(pregunta, idx) {
    let tipo = obtenerTipoPregunta(pregunta);
    let html = '';
    
    switch(tipo) {
        case 'multiple':
            html = renderizarMultiple(pregunta);
            break;
        case 'vf':
            html = renderizarVF(pregunta);
            break;
        case 'asociar':
            html = renderizarAsociar(pregunta, idx);
            break;
        default:
            html = renderizarMultiple(pregunta);
    }
    
    return html;
}

// ============================================
// TIPO 1: OPCIÓN MÚLTIPLE (3 opciones)
// ============================================
function renderizarMultiple(pregunta) {
    let html = `<div class="pregunta-texto">${pregunta.texto}</div>`;
    html += `<div class="opciones">`;
    for (let i = 0; i < pregunta.opciones.length; i++) {
        html += `<button class="opcion" data-tipo="multiple" data-valor="${i}">${pregunta.opciones[i]}</button>`;
    }
    html += `</div>`;
    return html;
}

// ============================================
// TIPO 2: VERDADERO / FALSO
// ============================================
function renderizarVF(pregunta) {
    let html = `<div class="pregunta-texto">${pregunta.texto}</div>`;
    html += `<div class="opciones vf-container" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">`;
    html += `<button class="opcion vf-btn" data-tipo="vf" data-valor="true" style="background: #4caf50; width: 150px; font-size: 1.5rem;">✅ Verdadero</button>`;
    html += `<button class="opcion vf-btn" data-tipo="vf" data-valor="false" style="background: #f44336; width: 150px; font-size: 1.5rem;">❌ Falso</button>`;
    html += `</div>`;
    return html;
}

// ============================================
// TIPO 3: ASOCIACIÓN / RELACIONAR
// ============================================
// Variable global para almacenar el estado de cada pregunta de asociación
if (typeof window.paresActuales === 'undefined') {
    window.paresActuales = {};
}

function renderizarAsociar(pregunta, idxPregunta) {
    // Inicializar estado de esta pregunta si no existe
    if (!window.paresActuales[idxPregunta]) {
        // Crear copia desordenada de los pares
        let paresDesordenados = [...pregunta.pares];
        // Desordenar para que no sea siempre igual
        for (let i = paresDesordenados.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [paresDesordenados[i], paresDesordenados[j]] = [paresDesordenados[j], paresDesordenados[i]];
        }
        
        window.paresActuales[idxPregunta] = {
            items: paresDesordenados.map(p => ({ 
                texto: p.texto, 
                seleccionado: false, 
                coincidencia: p.coincide 
            })),
            coincidencias: pregunta.pares.map(p => p.coincide),
            seleccionActual: null,
            completado: false,
            feedback: ''
        };
    }
    
    let estado = window.paresActuales[idxPregunta];
    
    // Si ya completó todas las asociaciones
    if (estado.completado) {
        return `<div class="pregunta-texto">✅ ¡Felicidades! Has completado todas las asociaciones.</div>`;
    }
    
    let html = `<div class="pregunta-texto">${pregunta.texto}</div>`;
    html += `<div class="asociar-container" style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; margin: 20px 0;">`;
    
    // Columna izquierda: elementos para asociar
    html += `<div class="columna-asociar" style="flex: 1; min-width: 200px; text-align: center;">`;
    html += `<h3 style="color: #1e3c72;">📌 Palabras</h3>`;
    html += `<div style="display: flex; flex-direction: column; gap: 12px;">`;
    for (let i = 0; i < estado.items.length; i++) {
        let item = estado.items[i];
        let claseSeleccionado = item.seleccionado ? 'asociar-seleccionado' : '';
        let estiloSeleccionado = item.seleccionado ? 'background: #c8e6c9; text-decoration: line-through; opacity: 0.7;' : '';
        html += `<button class="asociar-item ${claseSeleccionado}" data-idx="${i}" data-tipo="asociar-item" style="background: #42a5f5; color: white; border: none; border-radius: 40px; padding: 12px 20px; font-size: 1.2rem; cursor: pointer; margin: 5px; ${estiloSeleccionado}">${item.texto}</button>`;
    }
    html += `</div></div>`;
    
    // Columna derecha: coincidencias
    html += `<div class="columna-asociar" style="flex: 1; min-width: 200px; text-align: center;">`;
    html += `<h3 style="color: #1e3c72;">🎯 Coinciden con</h3>`;
    html += `<div style="display: flex; flex-direction: column; gap: 12px;">`;
    for (let i = 0; i < estado.coincidencias.length; i++) {
        let coincidencia = estado.coincidencias[i];
        let usada = estado.items.some(item => item.seleccionado && item.coincidencia === coincidencia);
        let estiloUsada = usada ? 'background: #aaa; cursor: not-allowed;' : 'background: #ff9800;';
        html += `<button class="asociar-coincidencia" data-valor="${coincidencia}" data-tipo="asociar-coincidencia" ${usada ? 'disabled' : ''} style="border: none; border-radius: 40px; padding: 12px 20px; font-size: 1.2rem; cursor: pointer; margin: 5px; ${estiloUsada} color: white;">${coincidencia}</button>`;
    }
    html += `</div></div>`;
    
    html += `</div>`;
    
    // Mostrar feedback si existe
    if (estado.feedback) {
        html += `<div id="feedbackAsociar" class="feedback" style="margin-top: 20px;">${estado.feedback}</div>`;
    } else {
        html += `<div id="feedbackAsociar" class="feedback" style="margin-top: 20px; display: none;"></div>`;
    }
    
    return html;
}

// ============================================
// VERIFICAR ASOCIACIÓN
// ============================================
function verificarAsociacion(pregunta, idxPregunta, itemIdx, coincidenciaValor) {
    let estado = window.paresActuales[idxPregunta];
    let item = estado.items[itemIdx];
    
    // Si ya estaba seleccionado, no hacer nada
    if (item.seleccionado) {
        mostrarFeedbackAsociacion('⚠️ Esta palabra ya fue asociada. Elige otra.', 'warning');
        return false;
    }
    
    // Verificar si coincide
    if (item.coincidencia === coincidenciaValor) {
        // Acierto
        item.seleccionado = true;
        reproducirSonido('sonidoCorrecto');
        mostrarFeedbackAsociacion(`✅ ¡Correcto! "${item.texto}" se asocia con "${coincidenciaValor}"`, 'success');
        
        // Verificar si ya completó todas
        let todosSeleccionados = estado.items.every(i => i.seleccionado === true);
        if (todosSeleccionados) {
            estado.completado = true;
            mostrarFeedbackAsociacion(`🎉 ¡Felicidades! Completaste todas las asociaciones. 🎉<br><br>${pregunta.explicacion}`, 'completado');
            if (pregunta.datoDivertido) {
                document.getElementById('feedbackAsociar').innerHTML += `<div class="dato-div">🎲 ${pregunta.datoDivertido}</div>`;
            }
            document.getElementById("btnSiguiente").style.display = "block";
            window.ultimaRespuestaCorrecta = true;
            // Actualizar estrellas y racha
            if (typeof actualizarEstrellas === 'function') {
                window.aciertos = (window.aciertos || 0) + 1;
                window.aciertosSeguidos = (window.aciertosSeguidos || 0) + 1;
                actualizarEstrellas();
            }
        } else {
            // No ha completado, refrescar la vista
            setTimeout(() => {
                // Recargar solo la parte de asociación
                let contenedor = document.getElementById("opcionesDiv");
                if (contenedor) {
                    let nuevoHtml = renderizarAsociar(pregunta, idxPregunta);
                    contenedor.innerHTML = nuevoHtml;
                    // Reconfigurar eventos
                    setTimeout(() => configurarEventosAsociar(pregunta, idxPregunta), 50);
                }
            }, 100);
        }
        return true;
    } else {
        // Error
        reproducirSonido('sonidoIncorrecto');
        mostrarFeedbackAsociacion(`❌ Incorrecto. "${item.texto}" NO se asocia con "${coincidenciaValor}". Intenta de nuevo.`, 'error');
        return false;
    }
}

function mostrarFeedbackAsociacion(mensaje, tipo) {
    let feedbackDiv = document.getElementById('feedbackAsociar');
    if (feedbackDiv) {
        feedbackDiv.style.display = 'block';
        let color = '';
        if (tipo === 'success') color = '#4caf50';
        else if (tipo === 'error') color = '#f44336';
        else if (tipo === 'warning') color = '#ff9800';
        else if (tipo === 'completado') color = '#2196f3';
        feedbackDiv.style.background = color;
        feedbackDiv.style.color = 'white';
        feedbackDiv.innerHTML = mensaje;
        setTimeout(() => {
            if (tipo !== 'completado') {
                feedbackDiv.style.display = 'none';
            }
        }, 2000);
    }
}

// ============================================
// CONFIGURAR EVENTOS PARA ASOCIACIÓN
// ============================================
function configurarEventosAsociar(pregunta, idxPregunta) {
    let items = document.querySelectorAll('.asociar-item');
    let coincidencias = document.querySelectorAll('.asociar-coincidencia');
    let seleccionActual = null;
    
    // Limpiar eventos anteriores (clonar y reemplazar para evitar duplicados)
    items.forEach(item => {
        let nuevoItem = item.cloneNode(true);
        item.parentNode.replaceChild(nuevoItem, item);
        nuevoItem.addEventListener('click', () => {
            // Remover selección anterior
            if (seleccionActual) {
                seleccionActual.style.background = '#42a5f5';
                seleccionActual.classList.remove('asociar-activo');
            }
            seleccionActual = nuevoItem;
            seleccionActual.style.background = '#ffeb3b';
            seleccionActual.style.color = '#1e3c72';
            seleccionActual.classList.add('asociar-activo');
        });
    });
    
    coincidencias.forEach(coin => {
        let nuevaCoin = coin.cloneNode(true);
        coin.parentNode.replaceChild(nuevaCoin, coin);
        if (!nuevaCoin.disabled) {
            nuevaCoin.addEventListener('click', () => {
                if (seleccionActual && !nuevaCoin.disabled) {
                    let itemIdx = parseInt(seleccionActual.dataset.idx);
                    let coincidenciaValor = nuevaCoin.dataset.valor;
                    verificarAsociacion(pregunta, idxPregunta, itemIdx, coincidenciaValor);
                    seleccionActual.style.background = '#42a5f5';
                    seleccionActual.classList.remove('asociar-activo');
                    seleccionActual = null;
                }
            });
        }
    });
}