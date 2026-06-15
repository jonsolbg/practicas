<?php
// ============================================
// PANEL DE PROGRESO - PARA PADRES
// ============================================

// Contraseña simple (cámbiala por la que quieras)
$clave_acceso = "admin123";  // ¡Cámbiala!

// Verificar autenticación
$autenticado = false;
if (isset($_POST['clave']) && $_POST['clave'] === $clave_acceso) {
    $autenticado = true;
    session_start();
    $_SESSION['padre_autenticado'] = true;
} elseif (isset($_SESSION['padre_autenticado']) && $_SESSION['padre_autenticado'] === true) {
    $autenticado = true;
}

// Función para leer todos los archivos de progreso
function leerProgresos($directorio = "datos") {
    $progresos = array();
    if (!is_dir($directorio)) return $progresos;
    
    $archivos = glob($directorio . "/*.json");
    foreach ($archivos as $archivo) {
        $contenido = file_get_contents($archivo);
        $datos = json_decode($contenido, true);
        if ($datos) {
            $progresos[] = $datos;
        }
    }
    
    // Ordenar por fecha (más reciente primero)
    usort($progresos, function($a, $b) {
        return strtotime($b['fecha']) - strtotime($a['fecha']);
    });
    
    return $progresos;
}

// Función para obtener estadísticas globales
function obtenerEstadisticas($progresos) {
    $stats = array(
        'total_practicas' => count($progresos),
        'total_preguntas' => 0,
        'total_aciertos' => 0,
        'mejor_practica' => 0,
        'mejor_tema' => '',
        'racha_maxima' => 0,
        'temas_practicados' => array()
    );
    
    foreach ($progresos as $p) {
        $stats['total_preguntas'] += $p['total_preguntas'];
        $stats['total_aciertos'] += $p['aciertos'];
        $porcentaje = ($p['aciertos'] / $p['total_preguntas']) * 100;
        if ($porcentaje > $stats['mejor_practica']) {
            $stats['mejor_practica'] = $porcentaje;
            $stats['mejor_tema'] = $p['tema_nombre'];
        }
        if ($p['racha_maxima'] > $stats['racha_maxima']) {
            $stats['racha_maxima'] = $p['racha_maxima'];
        }
        
        $tema = $p['tema_categoria'] . '/' . $p['tema_archivo'];
        if (!isset($stats['temas_practicados'][$tema])) {
            $stats['temas_practicados'][$tema] = array(
                'nombre' => $p['tema_nombre'],
                'categoria' => $p['tema_categoria'],
                'veces' => 0,
                'total_aciertos' => 0,
                'total_preguntas' => 0
            );
        }
        $stats['temas_practicados'][$tema]['veces']++;
        $stats['temas_practicados'][$tema]['total_aciertos'] += $p['aciertos'];
        $stats['temas_practicados'][$tema]['total_preguntas'] += $p['total_preguntas'];
    }
    
    return $stats;
}

$progresos = leerProgresos();
$stats = obtenerEstadisticas($progresos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 Panel de Progreso - Misión Aprender</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', 'Comic Neue', sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2b4c7c);
            color: #333;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            border-radius: 30px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header h1 {
            color: #1e3c72;
            font-size: 2.5rem;
        }
        .header p {
            color: #666;
            margin-top: 10px;
        }
        .logout-btn {
            background: #ff9800;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            cursor: pointer;
            margin-top: 15px;
            font-size: 0.9rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 25px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card .emoji {
            font-size: 2.5rem;
        }
        .stat-card .valor {
            font-size: 2rem;
            font-weight: bold;
            color: #1e3c72;
            margin: 10px 0;
        }
        .stat-card .label {
            color: #666;
            font-size: 0.9rem;
        }
        .tabla-container {
            background: white;
            border-radius: 25px;
            padding: 20px;
            margin-bottom: 30px;
            overflow-x: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .tabla-container h2 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #1e3c72;
            color: white;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .acierto-alto {
            color: #4caf50;
            font-weight: bold;
        }
        .acierto-medio {
            color: #ff9800;
            font-weight: bold;
        }
        .acierto-bajo {
            color: #f44336;
            font-weight: bold;
        }
        .login-box {
            background: white;
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            border-radius: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .login-box input {
            width: 100%;
            padding: 15px;
            margin: 15px 0;
            border: 2px solid #ddd;
            border-radius: 30px;
            font-size: 1rem;
        }
        .login-box button {
            background: #1e3c72;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 1.1rem;
            cursor: pointer;
        }
        .volver-btn {
            background: #4caf50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-top: 20px;
        }
        .vaciar-btn {
            background: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            margin-left: 10px;
        }
        .fecha {
            font-size: 0.8rem;
            color: #999;
        }
    </style>
</head>
<body>
<div class="container">
<?php if (!$autenticado): ?>
    <!-- Formulario de login -->
    <div class="login-box">
        <div style="font-size: 4rem;">📊</div>
        <h2>Panel de Progreso</h2>
        <p>Acceso para padres</p>
        <form method="POST">
            <input type="password" name="clave" placeholder="Contraseña" autocomplete="off">
            <button type="submit">🔓 Acceder</button>
        </form>
    </div>
<?php else: ?>
    <!-- Panel de progreso -->
    <div class="header">
        <h1>📊 Panel de Progreso</h1>
        <p>¡Mira lo bien que va tu hijo! 🎉</p>
        <form method="POST" style="display: inline;">
            <button type="submit" name="cerrar" value="1" class="logout-btn">🚪 Cerrar sesión</button>
        </form>
        <?php if (isset($_POST['vaciar']) && $_POST['vaciar'] === '1'): ?>
            <?php
            $archivos = glob("datos/*.json");
            foreach ($archivos as $archivo) {
                unlink($archivo);
            }
            echo "<p style='color:green; margin-top:10px;'>✅ Historial vaciado correctamente.</p>";
            header("Refresh:2");
            ?>
        <?php endif; ?>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="emoji">🎮</div>
            <div class="valor"><?php echo $stats['total_practicas']; ?></div>
            <div class="label">Prácticas realizadas</div>
        </div>
        <div class="stat-card">
            <div class="emoji">❓</div>
            <div class="valor"><?php echo $stats['total_preguntas']; ?></div>
            <div class="label">Preguntas respondidas</div>
        </div>
        <div class="stat-card">
            <div class="emoji">✅</div>
            <div class="valor"><?php echo $stats['total_aciertos']; ?></div>
            <div class="label">Aciertos totales</div>
        </div>
        <div class="stat-card">
            <div class="emoji">📈</div>
            <div class="valor"><?php echo $stats['total_preguntas'] > 0 ? round(($stats['total_aciertos'] / $stats['total_preguntas']) * 100) : 0; ?>%</div>
            <div class="label">Porcentaje global</div>
        </div>
        <div class="stat-card">
            <div class="emoji">🏆</div>
            <div class="valor"><?php echo round($stats['mejor_practica']); ?>%</div>
            <div class="label">Mejor práctica</div>
            <div class="fecha"><?php echo $stats['mejor_tema']; ?></div>
        </div>
        <div class="stat-card">
            <div class="emoji">🔥</div>
            <div class="valor"><?php echo $stats['racha_maxima']; ?></div>
            <div class="label">Máxima racha</div>
        </div>
    </div>

    <!-- Tabla de prácticas recientes -->
    <div class="tabla-container">
        <h2>📋 Historial de prácticas</h2>
        <?php if (count($progresos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>📅 Fecha</th>
                        <th>📚 Tema</th>
                        <th>✅ Aciertos</th>
                        <th>❌ Errores</th>
                        <th>📊 Porcentaje</th>
                        <th>🔥 Racha máxima</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($progresos as $p): ?>
                        <?php 
                        $porcentaje = ($p['aciertos'] / $p['total_preguntas']) * 100;
                        $clasePorcentaje = '';
                        if ($porcentaje >= 80) $clasePorcentaje = 'acierto-alto';
                        elseif ($porcentaje >= 50) $clasePorcentaje = 'acierto-medio';
                        else $clasePorcentaje = 'acierto-bajo';
                        ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?></td>
                            <td><strong><?php echo $p['tema_nombre']; ?></strong><br><span class="fecha"><?php echo $p['tema_categoria']; ?></span></td>
                            <td>✅ <?php echo $p['aciertos']; ?></td>
                            <td>❌ <?php echo ($p['total_preguntas'] - $p['aciertos']); ?></td>
                            <td class="<?php echo $clasePorcentaje; ?>"><?php echo round($porcentaje); ?>%</td>
                            <td>🔥 <?php echo $p['racha_maxima']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>📭 Aún no hay prácticas registradas. ¡Tu hijo debe completar una práctica para ver el progreso!</p>
        <?php endif; ?>
    </div>

    <!-- Resumen por tema -->
    <div class="tabla-container">
        <h2>📊 Resumen por tema</h2>
        <?php if (count($stats['temas_practicados']) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>📚 Tema</th>
                        <th>🎮 Veces practicado</th>
                        <th>✅ Aciertos totales</th>
                        <th>📊 Porcentaje promedio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['temas_practicados'] as $tema): ?>
                        <?php 
                        $porcentajeTema = ($tema['total_preguntas'] > 0) ? ($tema['total_aciertos'] / $tema['total_preguntas']) * 100 : 0;
                        ?>
                        <tr>
                            <td><strong><?php echo $tema['nombre']; ?></strong></td>
                            <td>🎮 <?php echo $tema['veces']; ?></td>
                            <td>✅ <?php echo $tema['total_aciertos']; ?> / <?php echo $tema['total_preguntas']; ?></td>
                            <td><?php echo round($porcentajeTema); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>📭 No hay datos suficientes para mostrar resumen por tema.</p>
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="../index.php" class="volver-btn">🏠 Volver al menú principal</a>
        <form method="POST" style="display: inline;">
            <button type="submit" name="vaciar" value="1" class="vaciar-btn" onclick="return confirm('¿Seguro que quieres borrar todo el historial? Esta acción no se puede deshacer.')">🗑️ Vaciar historial</button>
        </form>
    </div>

    <?php
    // Cerrar sesión
    if (isset($_POST['cerrar'])) {
        session_destroy();
        header("Refresh:0");
    }
    ?>
<?php endif; ?>
</div>
</body>
</html>