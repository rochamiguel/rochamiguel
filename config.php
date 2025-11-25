<?php
// Funções partilhadas para carregar .env e gerir a ementa

function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$lines) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || $trimmed[0] === '#') {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
        $key = trim($key);
        $value = trim($value);

        if ($key === '') {
            continue;
        }

        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv($key . '=' . $value);
    }
}

function envValue(string $key, $default = null)
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

function menuFilePath(): string
{
    return __DIR__ . '/menu.json';
}

function readMenuData(): array
{
    $defaults = [
        'start_date' => '',
        'end_date' => '',
        'menu' => [],
    ];

    $file = menuFilePath();
    if (!file_exists($file)) {
        return $defaults;
    }

    $content = file_get_contents($file);
    $data = json_decode($content ?: '', true);
    if (!is_array($data)) {
        return $defaults;
    }

    $data['menu'] = $data['menu'] ?? [];

    return array_merge($defaults, $data);
}

function saveMenuData(array $data): void
{
    $payload = [
        'start_date' => $data['start_date'] ?? '',
        'end_date' => $data['end_date'] ?? '',
        'menu' => $data['menu'] ?? [],
    ];

    file_put_contents(
        menuFilePath(),
        json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

loadEnv(__DIR__ . '/.env');
