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
        'background_image' => '',
        'menu' => [],
        'notes' => [],
        'theme' => [
            'accent' => '#ff6f00',
            'accent2' => '#ffb057',
            'cream' => '#fff7ec',
            'bg_dark' => '#0f0c0a',
            'ink' => '#0a0a0a',
        ],
        'typography' => [
            'body_font' => 'Rubik, sans-serif',
            'title_font' => 'Chewy, cursive',
            'base_size' => '16px',
            'title_size' => 'clamp(3.8rem, 8vw, 7rem)',
        ],
        'texts' => [
            'hero_badge' => 'Casa da Francesinha',
            'hero_title' => 'Kafé Kina',
            'hero_subtitle' => 'A francesinha que faz Olival sorrir.',
            'cta_label' => 'Passar à ementa',
            'menu_title' => 'Ementa da Semana',
            'menu_intro' => 'A melhor francesinha da região. Molho da Casa, pão estaladiço e uma mesa que sabe sempre bem.',
            'schedule' => 'Aberto todos os dias (exceto Domingo)',
            'footer_line1' => 'Kafé Kina | Rua Central do Olival 4162, Vila Nova de Gaia',
            'footer_line2' => 'Tel: 965 659 041 | Aberto todos os dias (exceto Domingo)',
            'notes_title' => 'Notas do dia',
        ],
        'hero_tags' => [
            'Casa da Francesinha',
            'Molho da Casa',
            'Conforto em forma de francesinha',
            'Simples, honesto, delicioso',
        ],
        'highlights' => [
            'Casa da Francesinha',
            'Molho da Casa',
            'Conforto em forma de francesinha',
            'Simples, honesto, delicioso',
        ],
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

    $data['menu'] = is_array($data['menu'] ?? null) ? $data['menu'] : [];
    $data['background_image'] = $data['background_image'] ?? '';
    $data['notes'] = is_array($data['notes'] ?? null) ? $data['notes'] : [];
    $data['theme'] = array_merge($defaults['theme'], $data['theme'] ?? []);
    $data['typography'] = array_merge($defaults['typography'], $data['typography'] ?? []);
    $data['texts'] = array_merge($defaults['texts'], $data['texts'] ?? []);
    $data['hero_tags'] = is_array($data['hero_tags'] ?? null) ? $data['hero_tags'] : [];
    $data['highlights'] = is_array($data['highlights'] ?? null) ? $data['highlights'] : [];

    return array_merge($defaults, $data);
}

function saveMenuData(array $data): void
{
    $payload = [
        'start_date' => $data['start_date'] ?? '',
        'end_date' => $data['end_date'] ?? '',
        'background_image' => $data['background_image'] ?? '',
        'menu' => $data['menu'] ?? [],
        'notes' => $data['notes'] ?? [],
        'theme' => $data['theme'] ?? [],
        'typography' => $data['typography'] ?? [],
        'texts' => $data['texts'] ?? [],
        'hero_tags' => $data['hero_tags'] ?? [],
        'highlights' => $data['highlights'] ?? [],
    ];

    file_put_contents(
        menuFilePath(),
        json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

loadEnv(__DIR__ . '/.env');
