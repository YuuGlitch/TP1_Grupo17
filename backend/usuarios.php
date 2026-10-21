<?php
declare(strict_types=1);
require "conexion.php";
require "servicios.php";

// Orígenes permitidos
$origen = $_SERVER["HTTP_ORIGIN"] ?? "";

$origenesPermitidos = [
    "http://127.0.0.1:5500",
    "http://localhost:5500"
];

if (in_array($origen, $origenesPermitidos, true)) {
    header("Access-Control-Allow-Origin: $origen");
    header("Vary: Origin");
}

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

/* IMPORTANTE */
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
http_response_code(200);
exit();
}
$metodo = $_SERVER["REQUEST_METHOD"];

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    
    $dsn = 'mysql:host=' . env_required('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . env_required('DB_NAME') . ';charset=utf8mb4';
    $pdo = new PDO($dsn, env_required('DB_USER'), env_required('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function respond(array $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function input_json(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if ($raw !== '' && json_last_error() !== JSON_ERROR_NONE) respond(['error' => 'El JSON enviado no es válido.'], 400);
    return is_array($data) ? $data : [];
}


function require_method(string ...$methods): string
{
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (!in_array($method, $methods, true)) {
        header('Allow: ' . implode(', ', $methods));
        respond(['error' => 'Método no permitido.'], 405);
    }
    return $method;
}

function env_required(string $name): string
{
    $value = getenv($name);
    if ($value === false || $value === '') throw new RuntimeException('Falta configurar la variable ' . $name);
    return $value;
}

try {
    $method = require_method('GET', 'POST', 'PUT', 'DELETE');
    if ($method === 'GET') {
        $stmt = db()->prepare('SELECT id, email, contrasenia, created_at');
        $stmt->execute([$userId]);
        respond(['usuario' => $stmt->fetchAll()]);
    }

    $data = input_json();
    if ($method === 'POST') {
        $email = trim((string) ($data['email'] ?? ''));
        $password = trim((string) ($data['contrasenia'] ?? ''));
        
        if ($email === '' || mb_strlen($email) > 150 || $password === '') respond(['error' => 'El mail y la contrasenia son obligatorios; el mail admite hasta 150 caracteres.'], 422);
        $stmt = db()->prepare('INSERT INTO usuario (email, contrasenia) VALUES (?, ?)');
        $stmt->execute([$email, $contrasenia]);
        respond(['mensaje' => 'Usuario creado.', 'id' => (int) db()->lastInsertId()], 201);
    }

    if ($method === 'PUT') {
        $email = trim((string) ($data['email'] ?? ''));
        $password = trim((string) ($data['contrasenia'] ?? ''));

        if ($email === '' || mb_strlen($email) > 150 || $password === '') respond(['error' => 'El mail y la contrasenia son obligatorios; el mail admite hasta 150 caracteres.'], 422);
        $check = db()->prepare('SELECT id FROM usuario WHERE id = ?');
        $check->execute([$id]);
        
        if (!$check->fetch()) respond(['error' => 'No se encontró ese usuario.'], 404);
        $stmt = db()->prepare('UPDATE usuario SET email = ?, contrasenia = ? WHERE id = ?');
        $stmt->execute([$email, $password, $id]);
        
    } else {
        $stmt = db()->prepare('DELETE FROM usuario WHERE id = ?');
        $stmt->execute([$id]);
    }

    if ($method === 'DELETE' && $stmt->rowCount() === 0) respond(['error' => 'No se encontró ese usuario.'], 404);
    respond(['mensaje' => $method === 'PUT' ? 'Usuario actualizada.' : 'Usuario eliminado.']);
} catch (Throwable $e) {
    error_log((string) $e);
    respond(['error' => 'No se pudo completar la operación. Revisa la conexión y la base de datos.'], 500);
}

