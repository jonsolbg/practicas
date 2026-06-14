// personaje.js - Controla el personaje guía
function cambiarMensajePersonaje(mensaje) {
    let msgDiv = document.getElementById("mensaje-personaje");
    if (msgDiv) {
        msgDiv.innerText = mensaje;
        msgDiv.style.transform = "scale(1.05)";
        setTimeout(() => { msgDiv.style.transform = "scale(1)"; }, 300);
    }
}

// Mensajes aleatorios de ánimo (opcional)
const mensajesAnimacion = [
    "💪 ¡Tú puedes!",
    "🧠 ¡Piensa con calma!",
    "🎯 Concéntrate, lo lograrás",
    "🌟 ¡Esa pregunta es fácil para ti!",
    "🐝 Zumba como una abeja y responde"
];

function mensajeAleatorio() {
    const idx = Math.floor(Math.random() * mensajesAnimacion.length);
    cambiarMensajePersonaje(mensajesAnimacion[idx]);
}

// Cambiar mensaje cada 30 segundos para mantener atención
setInterval(() => {
    if (!bloqueado) {
        mensajeAleatorio();
    }
}, 30000);
