// voz.js - Añade un botón para leer la pregunta en voz alta
if ('speechSynthesis' in window) {
    const contenedor = document.querySelector(".contenedor-pregunta");
    if (contenedor && !document.querySelector(".btn-voz")) {
        let botonVoz = document.createElement("button");
        botonVoz.innerText = "🔊 Leer pregunta";
        botonVoz.className = "btn-voz";
        botonVoz.onclick = () => {
            let texto = document.getElementById("preguntaTexto").innerText;
            if (texto && !texto.includes("Misión cumplida")) {
                let utterance = new SpeechSynthesisUtterance(texto);
                utterance.lang = 'es-ES';
                utterance.rate = 0.9;  // un poco más lento para mejor comprensión
                speechSynthesis.cancel();
                speechSynthesis.speak(utterance);
                cambiarMensajePersonaje("🔊 Escucha con atención...");
            }
        };
        contenedor.prepend(botonVoz);
    }
}
