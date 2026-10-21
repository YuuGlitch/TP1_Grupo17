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

function require_user(): int
{
    if (empty($_SESSION['usuario_id'])) respond(['error' => 'Inicia sesión para continuar.'], 401);
    return (int) $_SESSION['usuario_id'];
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
    $userId = require_user();
    $method = require_method('GET', 'POST', 'PUT', 'DELETE');
    if ($method === 'GET') {
        $stmt = db()->prepare('SELECT id, titulo, contenido, favorita, created_at, updated_at FROM notas WHERE usuario_id = ? ORDER BY updated_at DESC');
        $stmt->execute([$userId]);
        respond(['notas' => $stmt->fetchAll()]);
    }

    $data = input_json();
    if ($method === 'POST') {
        $title = trim((string) ($data['titulo'] ?? ''));
        $content = trim((string) ($data['contenido'] ?? ''));
        
        if ($title === '' || mb_strlen($title) > 150 || $content === '') respond(['error' => 'El título y el contenido son obligatorios; el título admite hasta 150 caracteres.'], 422);
        $stmt = db()->prepare('INSERT INTO notas (usuario_id, titulo, contenido, favorita) VALUES (?, ?, ?, ?)');
        $stmt->execute([$userId, $title, $content, !empty($data['favorita']) ? 1 : 0]);
        respond(['mensaje' => 'Nota creada.', 'id' => (int) db()->lastInsertId()], 201);
    }
    
    $id = filter_var($data['id'] ?? $_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) respond(['error' => 'Identificador de nota inválido.'], 422);
    if ($method === 'PUT') {
        $title = trim((string) ($data['titulo'] ?? ''));
        $content = trim((string) ($data['contenido'] ?? ''));
        
        if ($title === '' || mb_strlen($title) > 150 || $content === '') respond(['error' => 'El título y el contenido son obligatorios; el título admite hasta 150 caracteres.'], 422);
        $check = db()->prepare('SELECT id FROM notas WHERE id = ? AND usuario_id = ?');
        $check->execute([$id, $userId]);
        
        if (!$check->fetch()) respond(['error' => 'No se encontró esa nota.'], 404);
        $stmt = db()->prepare('UPDATE notas SET titulo = ?, contenido = ?, favorita = ? WHERE id = ? AND usuario_id = ?');
        $stmt->execute([$title, $content, !empty($data['favorita']) ? 1 : 0, $id, $userId]);
        
    } else {
        $stmt = db()->prepare('DELETE FROM notas WHERE id = ? AND usuario_id = ?');
        $stmt->execute([$id, $userId]);
    }

    if ($method === 'DELETE' && $stmt->rowCount() === 0) respond(['error' => 'No se encontró esa nota.'], 404);
    respond(['mensaje' => $method === 'PUT' ? 'Nota actualizada.' : 'Nota eliminada.']);
} catch (Throwable $e) {
    error_log((string) $e);
    respond(['error' => 'No se pudo completar la operación. Revisa la conexión y la base de datos.'], 500);
}