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

        $newMenu = [];
        foreach ($daysOfWeek as $day) {
            $newMenu[$day] = trim($_POST['menu'][$day] ?? '');
        }

        saveMenuData([
            'start_date' => $start,
            'end_date' => $end,
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
        body {
            font-family: Arial, sans-serif;
            background: #0f0f0f;
            color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .card {
            background: #1f1f1f;
            padding: 24px;
            margin: 40px auto;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            width: min(820px, 95%);
        }

        h1 {
            margin: 0 0 20px 0;
            font-size: 1.8rem;
            color: #ff6f00;
        }

        form + form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #ffc38a;
        }

        input[type="password"],
        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #333;
            background: #121212;
            color: #f5f5f5;
            margin-bottom: 14px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 14px 20px;
            margin-top: 12px;
        }

        .menu-grid .item {
            background: #171717;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #2c2c2c;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        button {
            background: linear-gradient(90deg, #ff6f00, #ff8c00);
            color: #fff;
            border: none;
            padding: 12px 18px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
        }

        button.secondary {
            background: #333;
            color: #f5f5f5;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 10px;
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
            margin-bottom: 16px;
            color: #c2c2c2;
            font-size: 0.95rem;
        }

        a { color: #ffb36b; }
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

                <label for="start_date">Data de início</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($menuData['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                <label for="end_date">Data de fim</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($menuData['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

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
