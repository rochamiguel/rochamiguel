<?php
session_start();
require __DIR__ . '/config.php';

$daysOfWeek = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
$menuData = readMenuData();
$storedPassword = envValue('ADMIN_PASSWORD', '');

$successMessage = '';
$errorMessage = '';

function isLoggedIn(): bool
{
    return !empty($_SESSION['logged_in']);
}

function splitLines(string $input): array
{
    $lines = preg_split('/\r\n|\r|\n/', $input);
    return array_values(array_filter(array_map('trim', $lines), 'strlen'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $providedPassword = $_POST['password'] ?? '';

        if ($storedPassword === '') {
            $errorMessage = 'Define a password ADMIN_PASSWORD no ficheiro .env.';
        } elseif ($providedPassword !== '' && hash_equals($storedPassword, $providedPassword)) {
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $successMessage = 'Autenticação feita. Podes editar a ementa.';
        } else {
            $errorMessage = 'Password incorreta.';
        }
    }

    if ($action === 'logout') {
        session_destroy();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    if ($action === 'save' && isLoggedIn()) {
        $start = trim($_POST['start_date'] ?? '');
        $end = trim($_POST['end_date'] ?? '');
        $bg = trim($_POST['background_image'] ?? '');
        $themeInput = $_POST['theme'] ?? [];
        $typographyInput = $_POST['typography'] ?? [];
        $textsInput = $_POST['texts'] ?? [];

        $newMenu = [];
        foreach ($daysOfWeek as $day) {
            $newMenu[$day] = trim($_POST['menu'][$day] ?? '');
        }

        $theme = [
            'accent' => trim($themeInput['accent'] ?? ($menuData['theme']['accent'] ?? '#ff6f00')),
            'accent2' => trim($themeInput['accent2'] ?? ($menuData['theme']['accent2'] ?? '#ffb057')),
            'cream' => trim($themeInput['cream'] ?? ($menuData['theme']['cream'] ?? '#fff7ec')),
            'bg_dark' => trim($themeInput['bg_dark'] ?? ($menuData['theme']['bg_dark'] ?? '#0f0c0a')),
            'ink' => trim($themeInput['ink'] ?? ($menuData['theme']['ink'] ?? '#0a0a0a')),
        ];

        $typography = [
            'body_font' => trim($typographyInput['body_font'] ?? ($menuData['typography']['body_font'] ?? 'Rubik, sans-serif')),
            'title_font' => trim($typographyInput['title_font'] ?? ($menuData['typography']['title_font'] ?? 'Chewy, cursive')),
            'base_size' => trim($typographyInput['base_size'] ?? ($menuData['typography']['base_size'] ?? '16px')),
            'title_size' => trim($typographyInput['title_size'] ?? ($menuData['typography']['title_size'] ?? 'clamp(3.8rem, 8vw, 7rem)')),
        ];

        $texts = [
            'hero_badge' => trim($textsInput['hero_badge'] ?? ($menuData['texts']['hero_badge'] ?? '')),
            'hero_title' => trim($textsInput['hero_title'] ?? ($menuData['texts']['hero_title'] ?? '')),
            'hero_subtitle' => trim($textsInput['hero_subtitle'] ?? ($menuData['texts']['hero_subtitle'] ?? '')),
            'cta_label' => trim($textsInput['cta_label'] ?? ($menuData['texts']['cta_label'] ?? '')),
            'menu_title' => trim($textsInput['menu_title'] ?? ($menuData['texts']['menu_title'] ?? '')),
            'menu_intro' => trim($textsInput['menu_intro'] ?? ($menuData['texts']['menu_intro'] ?? '')),
            'schedule' => trim($textsInput['schedule'] ?? ($menuData['texts']['schedule'] ?? '')),
            'footer_line1' => trim($textsInput['footer_line1'] ?? ($menuData['texts']['footer_line1'] ?? '')),
            'footer_line2' => trim($textsInput['footer_line2'] ?? ($menuData['texts']['footer_line2'] ?? '')),
            'notes_title' => trim($textsInput['notes_title'] ?? ($menuData['texts']['notes_title'] ?? '')),
        ];

        $notes = splitLines($_POST['notes_raw'] ?? '');
        $heroTags = splitLines($_POST['hero_tags_raw'] ?? '');
        $highlights = splitLines($_POST['highlights_raw'] ?? '');

        saveMenuData([
            'start_date' => $start,
            'end_date' => $end,
            'background_image' => $bg,
            'menu' => $newMenu,
            'notes' => $notes,
            'theme' => $theme,
            'typography' => $typography,
            'texts' => $texts,
            'hero_tags' => $heroTags,
            'highlights' => $highlights,
        ]);

        $menuData = readMenuData();
        $successMessage = 'Ementa atualizada com sucesso!';
    }
}

$heroTagsText = implode("\n", $menuData['hero_tags'] ?? []);
$highlightsText = implode("\n", $menuData['highlights'] ?? []);
$notesText = implode("\n", $menuData['notes'] ?? []);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backoffice | Kafé Kina</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Rubik', Arial, sans-serif;
            background: radial-gradient(circle at 20% 20%, #1c1c1c, #0f0f0f);
            color: #f5f5f5;
            margin: 0;
            padding: 0 16px 40px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .card {
            background: #151515;
            padding: 26px;
            margin: 40px auto 0;
            border-radius: 16px;
            box-shadow: 0 16px 45px rgba(0, 0, 0, 0.45);
            width: min(960px, 100%);
            border: 1px solid #262626;
        }

        h1 {
            margin: 0 0 18px 0;
            font-size: 1.9rem;
            color: #ff6f00;
            letter-spacing: 0.5px;
        }

        form + form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #ffc38a;
            letter-spacing: 0.2px;
        }

        input[type="password"],
        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 12px 12px;
            border-radius: 10px;
            border: 1px solid #2e2e2e;
            background: #0d0d0d;
            color: #f5f5f5;
            margin-bottom: 16px;
            font-size: 0.98rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        input:focus {
            outline: none;
            border-color: #ff8c00;
            box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.15);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px 20px;
            margin-top: 12px;
        }

        .menu-grid .item {
            background: #111;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid #232323;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.03);
        }

        .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        button {
            background: linear-gradient(120deg, #ff6f00, #ff9a3c);
            color: #fff;
            border: none;
            padding: 12px 18px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 800;
            letter-spacing: 0.2px;
            box-shadow: 0 8px 20px rgba(255, 111, 0, 0.25);
            transition: transform 0.1s ease, box-shadow 0.1s ease;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 26px rgba(255, 111, 0, 0.35);
        }

        button.secondary {
            background: #2c2c2c;
            color: #f5f5f5;
            box-shadow: none;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 14px;
        }

        .alert.success {
            background: #15311f;
            color: #9df3c4;
            border: 1px solid #276f3d;
        }

        .alert.error {
            background: #3a1111;
            color: #f6b1b1;
            border: 1px solid #7a1d1d;
        }

        .top-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            color: #c2c2c2;
            font-size: 0.95rem;
        }

        .top-links a {
            color: #ffb36b;
            text-decoration: none;
            font-weight: 700;
        }

        .top-links a:hover { color: #ffd0a2; }

        .section-title {
            margin: 10px 0 8px;
            color: #f1f1f1;
            font-size: 1.05rem;
            letter-spacing: 0.3px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="top-links">
            <div>Backoffice Kafé Kina</div>
            <div><a href="kafe.php">Ver site</a></div>
        </div>
        <h1>Gerir Ementa</h1>

        <?php if ($successMessage !== ''): ?>
            <div class="alert success"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($errorMessage !== ''): ?>
            <div class="alert error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!isLoggedIn()): ?>
            <form method="post">
                <input type="hidden" name="action" value="login">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
                <button type="submit">Entrar</button>
            </form>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="action" value="save">

                <div class="row">
                    <div>
                        <label for="start_date">Data de início</label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($menuData['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div>
                        <label for="end_date">Data de fim</label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($menuData['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <label for="background_image" class="section-title">Imagem de fundo (URL)</label>
                <input
                    type="text"
                    id="background_image"
                    name="background_image"
                    placeholder="https://"
                    value="<?php echo htmlspecialchars($menuData['background_image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                >

                <h3 class="section-title">Textos principais</h3>
                <div class="row">
                    <div>
                        <label for="hero_title">Título</label>
                        <input type="text" id="hero_title" name="texts[hero_title]" value="<?php echo htmlspecialchars($menuData['texts']['hero_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div>
                        <label for="hero_badge">Selo/Bagde</label>
                        <input type="text" id="hero_badge" name="texts[hero_badge]" value="<?php echo htmlspecialchars($menuData['texts']['hero_badge'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <label for="hero_subtitle">Subtítulo</label>
                <input type="text" id="hero_subtitle" name="texts[hero_subtitle]" value="<?php echo htmlspecialchars($menuData['texts']['hero_subtitle'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                <label for="cta_label">Texto do botão</label>
                <input type="text" id="cta_label" name="texts[cta_label]" value="<?php echo htmlspecialchars($menuData['texts']['cta_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                <div class="row">
                    <div>
                        <label for="menu_title">Título da ementa</label>
                        <input type="text" id="menu_title" name="texts[menu_title]" value="<?php echo htmlspecialchars($menuData['texts']['menu_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div>
                        <label for="schedule">Horário / aviso</label>
                        <input type="text" id="schedule" name="texts[schedule]" value="<?php echo htmlspecialchars($menuData['texts']['schedule'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <label for="menu_intro">Intro da ementa</label>
                <input type="text" id="menu_intro" name="texts[menu_intro]" value="<?php echo htmlspecialchars($menuData['texts']['menu_intro'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                <div class="row">
                    <div>
                        <label for="footer_line1">Linha de rodapé 1</label>
                        <input type="text" id="footer_line1" name="texts[footer_line1]" value="<?php echo htmlspecialchars($menuData['texts']['footer_line1'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div>
                        <label for="footer_line2">Linha de rodapé 2</label>
                        <input type="text" id="footer_line2" name="texts[footer_line2]" value="<?php echo htmlspecialchars($menuData['texts']['footer_line2'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <h3 class="section-title">Listas (um item por linha)</h3>
                <label for="hero_tags_raw">Badges do topo</label>
                <textarea id="hero_tags_raw" name="hero_tags_raw" rows="3" style="width:100%; padding:12px; border-radius:10px; border:1px solid #2e2e2e; background:#0d0d0d; color:#f5f5f5;"><?php echo htmlspecialchars($heroTagsText, ENT_QUOTES, 'UTF-8'); ?></textarea>

                <label for="highlights_raw" style="margin-top:10px;">Destaques da ementa</label>
                <textarea id="highlights_raw" name="highlights_raw" rows="3" style="width:100%; padding:12px; border-radius:10px; border:1px solid #2e2e2e; background:#0d0d0d; color:#f5f5f5;"><?php echo htmlspecialchars($highlightsText, ENT_QUOTES, 'UTF-8'); ?></textarea>

                <label for="notes_raw" style="margin-top:10px;">Notas (uma por linha)</label>
                <textarea id="notes_raw" name="notes_raw" rows="3" style="width:100%; padding:12px; border-radius:10px; border:1px solid #2e2e2e; background:#0d0d0d; color:#f5f5f5;"><?php echo htmlspecialchars($notesText, ENT_QUOTES, 'UTF-8'); ?></textarea>

                <h3 class="section-title">Tema e tipografia</h3>
                <div class="row">
                    <div>
                        <label for="body_font">Fonte principal</label>
                        <input type="text" id="body_font" name="typography[body_font]" value="<?php echo htmlspecialchars($menuData['typography']['body_font'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="ex: 'Rubik, sans-serif'">
                    </div>
                    <div>
                        <label for="title_font">Fonte do título</label>
                        <input type="text" id="title_font" name="typography[title_font]" value="<?php echo htmlspecialchars($menuData['typography']['title_font'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="ex: 'Chewy, cursive'">
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="base_size">Tamanho base</label>
                        <input type="text" id="base_size" name="typography[base_size]" value="<?php echo htmlspecialchars($menuData['typography']['base_size'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="16px">
                    </div>
                    <div>
                        <label for="title_size">Tamanho do título</label>
                        <input type="text" id="title_size" name="typography[title_size]" value="<?php echo htmlspecialchars($menuData['typography']['title_size'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="clamp(3.8rem, 8vw, 7rem)">
                    </div>
                </div>

                <div class="row">
                    <div>
                        <label for="accent">Cor destaque</label>
                        <input type="color" id="accent" name="theme[accent]" value="<?php echo htmlspecialchars($menuData['theme']['accent'] ?? '#ff6f00', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div>
                        <label for="accent2">Cor destaque 2</label>
                        <input type="color" id="accent2" name="theme[accent2]" value="<?php echo htmlspecialchars($menuData['theme']['accent2'] ?? '#ffb057', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label for="cream">Cor creme</label>
                        <input type="color" id="cream" name="theme[cream]" value="<?php echo htmlspecialchars($menuData['theme']['cream'] ?? '#fff7ec', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div>
                        <label for="bg_dark">Cor fundo</label>
                        <input type="color" id="bg_dark" name="theme[bg_dark]" value="<?php echo htmlspecialchars($menuData['theme']['bg_dark'] ?? '#0f0c0a', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div>
                        <label for="ink">Cor texto</label>
                        <input type="color" id="ink" name="theme[ink]" value="<?php echo htmlspecialchars($menuData['theme']['ink'] ?? '#0a0a0a', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <div class="menu-grid">
                    <?php foreach ($daysOfWeek as $day): ?>
                        <div class="item">
                            <label for="menu-<?php echo htmlspecialchars($day, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($day, ENT_QUOTES, 'UTF-8'); ?></label>
                            <input
                                type="text"
                                id="menu-<?php echo htmlspecialchars($day, ENT_QUOTES, 'UTF-8'); ?>"
                                name="menu[<?php echo htmlspecialchars($day, ENT_QUOTES, 'UTF-8'); ?>]"
                                value="<?php echo htmlspecialchars($menuData['menu'][$day] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                placeholder="Prato do dia"
                            >
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="actions" style="margin-top: 16px;">
                    <button type="submit">Guardar ementa</button>
                </div>
            </form>

            <form method="post" class="actions" style="margin-top: 14px;">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="secondary">Terminar sessão</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
