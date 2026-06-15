<?php
// guardar_progreso.php - Recibe datos del progreso y los guarda

// Configuración
$directorioDestino = "progreso/datos/";

// Crear directorio si no existe
if (!file_exists($directorioDestino)) {
    mkdir($directorioDestino, 0777, true);
}

// Leer datos del POST
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos) {
    http_response_code(400);
    echo json_encode(['error' => 'No se recibieron datos']);
    exit;
}

// Generar nombre único para el archivo
$fecha = date('Y-m-d_H-i-s');
$nombreArchivo = $directorioDestino . $fecha . '_' . uniqid() . '.json';

// Guardar archivo
$archivo = fopen($nombreArchivo, 'w');
fwrite($archivo, json_encode($datos, JSON_PRETTY_PRINT));
fclose($archivo);

// Respuesta exitosa
echo json_encode(['success' => true, 'archivo' => $nombreArchivo]);
?>