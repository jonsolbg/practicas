<pre>
Necesito un archivo JSON para un juego educativo interactivo de preguntas y respuestas.

El sistema soporta TRES tipos de preguntas:

1. **Múltiple (tipo: "multiple")** - 4 opciones, una correcta
2. **Verdadero/Falso (tipo: "vf")** - Afirmación que es verdadera o falsa
3. **Asociación (tipo: "asociar")** - Unir elementos de dos columnas

## Formato para cada tipo:

### Tipo 1: Múltiple (es el que usabas antes, sigue igual)
{
  "titulo": "[Título llamativo con emoji]",
  "colorFondo": "#hexadecimal",
  "colorBoton": "#hexadecimal",
  "preguntas": [
    {
      "id": 1,
      "tipo": "multiple",
      "texto": "[Pregunta corta, máximo 12 palabras]",
      "opciones": ["Opción A", "Opción B", "Opción C", "Opción D"],
      "correcta": 0,
      "explicacion": "[Explicación positiva]",
      "datoDivertido": "[Dato curioso con emoji]"
    }
  ]
}

### Tipo 2: Verdadero / Falso
{
  "titulo": "[Título con emoji]",
  "colorFondo": "#hexadecimal",
  "colorBoton": "#hexadecimal",
  "preguntas": [
    {
      "id": 1,
      "tipo": "vf",
      "texto": "[Afirmación que es verdadera o falsa]",
      "correcta": true,
      "explicacion": "[Explicación de por qué es verdadero o falso]",
      "datoDivertido": "[Dato curioso]"
    }
  ]
}

### Tipo 3: Asociación / Relacionar
{
  "titulo": "[Título con emoji]",
  "colorFondo": "#hexadecimal",
  "colorBoton": "#hexadecimal",
  "preguntas": [
    {
      "id": 1,
      "tipo": "asociar",
      "texto": "[Indicación de lo que hay que asociar]",
      "pares": [
        {"texto": "Elemento 1", "coincide": "Su pareja 1"},
        {"texto": "Elemento 2", "coincide": "Su pareja 2"},
        {"texto": "Elemento 3", "coincide": "Su pareja 3"}
      ],
      "explicacion": "[Explicación del tema]",
      "datoDivertido": "[Dato curioso]"
    }
  ]
}

## Reglas importantes:
- Las preguntas de asociación deben tener entre 3 y 5 pares
- Usa emojis para hacerlo divertido
- No des pistas de la respuesta correcta en el texto de la pregunta.
- Explicaciones cortas y positivas
- Colores vivos, hagámoslo atractivo para niños
- Mezcla los 3 tipos de preguntas en el mismo archivo para variedad pero agrupando, primero algunas de múltiple, luego verdadero/falso y finalmente asociación.
- De 10 a 12 preguntas por tema. Pueden ser más si son de tipo "asociación" o "verdadero/falso".
- Genera un archivo .json por cada tema, con un título atractivo y emojis relacionados al tema.

## Tema del JSON:
[TEMA AQUÍ]

## Cantidad de preguntas:
[NÚMERO, máximo 10]
/*opcional*/ Cantidad de preguntas: [número, ej: 6]

--------------------------------------------

Genera un archivo JSON para juego de preguntas. Tema: [TU TEMA]. 
Formato: {"titulo":"...","colorFondo":"#...","colorBoton":"#...","preguntas":[{"id":1,"texto":"...","opciones":["...","...","..."],"correcta":0,"explicacion":"...","datoDivertido":"..."}]}
Edad: [EDAD]. Haz 6 preguntas cortas con emojis.
</pre>