<?php
// Escanea la carpeta data/ y lista los archivos .json
$temas = [];
foreach (glob("data/*.json") as $archivo) {
    $nombre = basename($archivo, ".json");
    $temas[] = $nombre;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>🎮 Misión Aprender - Elige tu práctica</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2b4c7c);
            font-family: 'Comic Neue', 'Segoe UI', 'Comic Neue', 'Chalkboard SE', cursive;
            text-align: center;
            color: white;
            padding: 20px;
        }
        .menu {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
        }
        .tarjeta {
            background: rgba(255,255,240,0.9);
            color: #1e3c72;
            border-radius: 40px;
            padding: 20px 30px;
            width: 220px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            font-size: 1.8rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .tarjeta:hover {
            transform: scale(1.05);
            background: #ffffcc;
            box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        }
        h1 {
            font-size: 3rem;
            text-shadow: 4px 4px 0 #0a2a44;
        }
        .personaje-bienvenida {
            margin: 20px auto;
            max-width: 150px;
        }
    </style>
</head>
<body>
    <div class="personaje-bienvenida">🧸🧠</div>
    <h1>🚀 ¡Elige tu misión!</h1>
    <p>Haz clic en el tema que quieres practicar. ¡Gana estrellas!</p>
    <div class="menu">
        <?php foreach ($temas as $tema): ?>
            <a href="practica.php?tema=<?= urlencode($tema) ?>" class="tarjeta">
                <?= ucfirst($tema) ?>
            </a>
        <?php endforeach; ?>
    </div>
    <p style="margin-top: 50px;">✨ Cada acierto suma una estrella ✨</p>
</body>
</html>
