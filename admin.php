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

        $newMenu = [];
        foreach ($daysOfWeek as $day) {
            $newMenu[$day] = trim($_POST['menu'][$day] ?? '');
        }

        saveMenuData([
            'start_date' => $start,
            'end_date' => $end,
            'background_image' => $bg,
            'menu' => $newMenu,
        ]);

        $menuData = readMenuData();
        $successMessage = 'Ementa atualizada com sucesso!';
    }
}
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
