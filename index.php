<?php
$temas = [];
foreach (glob("data/*.json") as $archivo) {
    $temas[] = basename($archivo, ".json");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>🎮 Misión Aprender</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2b4c7c);
            font-family: 'Comic Neue', 'Segoe UI', cursive;
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
            font-size: 1.8rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }
        .tarjeta:hover {
            transform: scale(1.05);
            background: #ffffcc;
        }
        h1 { font-size: 3rem; text-shadow: 4px 4px 0 #0a2a44; }
    </style>
</head>
<body>
    <h1>🚀 ¡Elige tu misión!</h1>
    <div class="menu">
        <?php foreach ($temas as $tema): ?>
            <a href="practica.php?tema=<?= urlencode($tema) ?>" class="tarjeta">
                <?= ucfirst(str_replace('_', ' ', $tema)) ?>
            </a>
        <?php endforeach; ?>
    </div>
    <p style="margin-top: 50px;">✨ Cada acierto suma una estrella ✨</p>
</body>
</html>