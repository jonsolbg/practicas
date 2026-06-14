<pre>
Necesito un archivo JSON para un juego educativo interactivo de preguntas y respuestas. 
El formato debe ser exactamente el siguiente:

{
  "titulo": "[Título llamativo con emoji]",
  "colorFondo": "[código hexadecimal color claro]",
  "colorBoton": "[código hexadecimal color vibrante]",
  "preguntas": [
    {
      "id": 1,
      "texto": "[Pregunta corta y clara, máximo 12 palabras]",
      "opciones": ["[Opción A]", "[Opción B]", "[Opción C]"],
      "correcta": [0, 1 o 2 según cuál sea la correcta],
      "explicacion": "[Explicación de una línea, lenguaje positivo]",
      "datoDivertido": "[Dato curioso con emoji, para motivar]"
    }
  ]
}

/*opcional*/ Cantidad de preguntas: [número, ej: 6]
Estilo: preguntas muy cortas, lenguaje divertido, usar emojis.
</pre>