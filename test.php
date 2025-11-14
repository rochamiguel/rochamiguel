<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$name = trim($_POST['name'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $message === '') {
    http_response_code(400);
    echo json_encode([
        'message' => 'Falta preencher o nome e a mensagem.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$time = (new DateTime('now', new DateTimeZone('Europe/Lisbon')))->format('H:i:s');
$response = sprintf(
    'Olá %s! O PHP recebeu "%s" às %s.',
    htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
    htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
    $time
);

echo json_encode(['message' => $response], JSON_UNESCAPED_UNICODE);
