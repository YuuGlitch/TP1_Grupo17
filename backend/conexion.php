<?php
require_once 'config.php';

if (ENTORNO === "produccion") {
    ini_set("display_errors", "0");
    ini_set("log_errors", "1");
}


try {
    $conexion = new PDO(
        "mysql:host=". DB_HOST. ";port=". DB_PORT";dbname=". DB_NAME.";charset=utf8mb4",
        DB_USER,    
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    error_log("Conexión fallida: ". $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "No se pudo conectar a la base de datos"]);
    exit();
}

function entorno($nombre, $porDefecto = ""){
    $valor = getenv($nombre);
    return $valor !== false ? $valor : $porDefecto;
}

define("DB_HOST", entorno("DB_HOST","localhost"));
define("DB_PORT", entorno("DB_PORT","3306"));
define("DB_NAME", entorno("DB_NAME","AppNotas"));
define("DB_USER", entorno("DB_USER","root"));
define("DB_PASS", entorno("DB_PASS",""));

define("ENTORNO", entorno("ENTORNO","local"));