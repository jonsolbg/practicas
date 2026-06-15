<?php
// ============================================
// CARGAR CONFIGURACIÓN
// ============================================
$config = array();
if (file_exists("config.json")) {
    $configJson = file_get_contents("config.json");
    $config = json_decode($configJson, true);
}

// Valores por defecto si no existen en config.json
$ordenCategorias = isset($config['ordenCategorias']) ? $config['ordenCategorias'] : array();
$nombresBonitos = isset($config['nombresBonitos']) ? $config['nombresBonitos'] : array();
$temasOcultos = isset($config['temasOcultos']) ? $config['temasOcultos'] : array();
$mostrarCategoriasVacias = isset($config['mostrarCategoriasVacias']) ? $config['mostrarCategoriasVacias'] : false;
$colorFondoGeneral = isset($config['colorFondoGeneral']) ? $config['colorFondoGeneral'] : '#1e3c72';
$colorFondoGradiente = isset($config['colorFondoGradiente']) ? $config['colorFondoGradiente'] : '#2b4c7c';

// ============================================
// FUNCIÓN PARA ESCANEAR TEMAS
// ============================================
function obtenerTemas($directorioBase = "data", $temasOcultos = array()) {
    $categorias = array();
    
    if (!is_dir($directorioBase)) {
        return $categorias;
    }
    
    $carpetas = glob($directorioBase . "/*", GLOB_ONLYDIR);
    
    foreach ($carpetas as $carpeta) {
        $nombreCategoria = basename($carpeta);
        $temas = array();
        
        $archivos = glob($carpeta . "/*.json");
        foreach ($archivos as $archivo) {
            $nombreTema = basename($archivo, ".json");
            
            // Ocultar temas si están en la lista negra
            if (in_array($nombreTema, $temasOcultos)) {
                continue;
            }
            
            $contenido = file_get_contents($archivo);
            $datos = json_decode($contenido, true);
            $titulo = isset($datos['titulo']) ? $datos['titulo'] : ucfirst(str_replace('_', ' ', $nombreTema));
            $orden = isset($datos['orden']) ? $datos['orden'] : 999;
            $temas[] = array(
                'archivo' => $nombreTema,
                'titulo' => $titulo,
                'ruta' => $carpeta . "/" . $archivo,
                'orden' => $orden
            );
        }
        
        // Ordenar temas dentro de la categoría por el campo 'orden'
        usort($temas, function($a, $b) {
            return $a['orden'] - $b['orden'];
        });
        
        if (count($temas) > 0) {
            $categorias[$nombreCategoria] = $temas;
        }
    }
    
    return $categorias;
}

// ============================================
// ORDENAR CATEGORÍAS SEGÚN CONFIG
// ============================================
function ordenarCategorias($categorias, $orden) {
    $ordenadas = array();
    
    // Primero agrega las que están en el orden definido
    foreach ($orden as $cat) {
        if (isset($categorias[$cat])) {
            $ordenadas[$cat] = $categorias[$cat];
            unset($categorias[$cat]);
        }
    }
    
    // Luego agrega el resto al final
    foreach ($categorias as $cat => $temas) {
        $ordenadas[$cat] = $temas;
    }
    
    return $ordenadas;
}

// ============================================
// EJECUTAR
// ============================================
$categorias = obtenerTemas("data", $temasOcultos);
$categorias = ordenarCategorias($categorias, $ordenCategorias);

// Si no hay categorías, mostrar mensaje amigable
$hayCategorias = count($categorias) > 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>🎮 Misión Aprender - Elige tu práctica</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        body {
            background: linear-gradient(135deg, <?php echo $colorFondoGeneral; ?>, <?php echo $colorFondoGradiente; ?>);
            font-family: 'Comic Neue', 'Segoe UI', 'Comic Neue', 'Chalkboard SE', cursive;
            text-align: center;
            color: white;
            padding: 20px;
        }
        h1 {
            font-size: 3rem;
            text-shadow: 4px 4px 0 rgba(0,0,0,0.3);
            margin-bottom: 10px;
        }
        .subtitulo {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        .categoria {
            margin: 40px 0;
            text-align: left;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
        .categoria-titulo {
            font-size: 2rem;
            background: rgba(255,255,255,0.2);
            display: inline-block;
            padding: 8px 25px;
            border-radius: 50px;
            margin-bottom: 20px;
            backdrop-filter: blur(5px);
        }
        .menu {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
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
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .tarjeta:hover {
            transform: scale(1.05);
            background: #ffffcc;
            box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        }
        .personaje-bienvenida {
            font-size: 4rem;
            margin: 20px auto;
        }
        footer {
            margin-top: 50px;
            font-size: 0.9rem;
            opacity: 0.7;
        }
        .mensaje-vacio {
            background: rgba(0,0,0,0.5);
            padding: 40px;
            border-radius: 40px;
            margin: 40px auto;
            max-width: 600px;
        }
        @media (max-width: 768px) {
            .tarjeta {
                width: 160px;
                padding: 15px 20px;
                font-size: 1.2rem;
            }
            .categoria-titulo {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="personaje-bienvenida">🧠🐝</div>
    <h1>🚀 ¡Elige tu misión!</h1>
    <div class="subtitulo">⭐ Cada acierto suma una estrella ⭐</div>

    <?php if ($hayCategorias): ?>
        <?php foreach ($categorias as $nombreCategoria => $temas): ?>
            <div class="categoria">
                <div class="categoria-titulo">
                    <?php 
                        // Usa el nombre bonito de config.json si existe
                        if (isset($nombresBonitos[$nombreCategoria])) {
                            echo $nombresBonitos[$nombreCategoria];
                        } else {
                            // Si no hay nombre bonito, traduce automáticamente
                            $traducciones = [
                                'ciencias' => '🔬 Ciencias Naturales',
                                'lenguaje' => '📚 Lenguaje y Español',
                                'matematicas' => '🧮 Matemáticas',
                                'historia' => '📜 Historia',
                                'geografia' => '🌎 Geografía',
                                'ingles' => '🇺🇸 Inglés',
                                'arte' => '🎨 Arte y Música'
                            ];
                            echo isset($traducciones[$nombreCategoria]) ? $traducciones[$nombreCategoria] : ucfirst($nombreCategoria);
                        }
                    ?>
                </div>
                <div class="menu">
                    <?php foreach ($temas as $tema): ?>
                        <a href="practica.php?tema=<?php echo urlencode($nombreCategoria . '/' . $tema['archivo']); ?>" class="tarjeta">
                            <?php echo $tema['titulo']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="mensaje-vacio">
            <p>📂 No hay temas disponibles.</p>
            <p>Agrega archivos JSON en la carpeta <strong>data/</strong> con sus subcarpetas.</p>
            <p>Ejemplo: <strong>data/ciencias/renal.json</strong></p>
        </div>
    <?php endif; ?>

    <footer>
        ✨ ¡Aprender es divertido! ✨
    </footer>
</body>
</html>