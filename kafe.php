<?php
require __DIR__ . '/config.php';

$menuData = readMenuData();
$daysOfWeek = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
$backgroundImage = $menuData['background_image'] !== '' ? $menuData['background_image'] : 'https://i.imgur.com/uRj0p6o.png';

function formatDateLabel(?string $date): ?string
{
    if (!$date) {
        return null;
    }

    $dt = DateTime::createFromFormat('Y-m-d', $date);
    if ($dt instanceof DateTime) {
        return $dt->format('d/m/Y');
    }

    return $date;
}

$startLabel = formatDateLabel($menuData['start_date'] ?? '') ?? '';
$endLabel = formatDateLabel($menuData['end_date'] ?? '') ?? '';

if ($startLabel !== '' && $endLabel !== '') {
    $rangeText = $startLabel . ' a ' . $endLabel;
} elseif ($startLabel !== '') {
    $rangeText = 'A partir de ' . $startLabel;
} elseif ($endLabel !== '') {
    $rangeText = 'Até ' . $endLabel;
} else {
    $rangeText = '';
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kafé Kina | A Melhor Francesinha de Olival</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Rubik:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-dark: #0f0c0a;
            --bg-panel: #18120f;
            --accent: #ff6f00;
            --accent-2: #ffb057;
            --cream: #fff7ec;
            --ink: #0a0a0a;
            --shadow: rgba(0, 0, 0, 0.4);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Rubik', sans-serif;
            background: radial-gradient(circle at 20% 20%, #1c120d, var(--bg-dark));
            color: #fdfdfd;
            overflow-x: hidden;
        }

        /* --- TIPOGRAFIA "MOLHO KINA" --- */
        .sauce-text {
            font-family: 'Chewy', cursive;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow:
                4px 4px 0px #b33e00,
                6px 6px 15px rgba(0,0,0,0.6),
                inset 2px 2px 5px rgba(255, 255, 255, 0.5);
            position: relative;
            z-index: 10;
        }

        /* --- HERO --- */
        .hero-section {
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background-image: linear-gradient(180deg, rgba(0,0,0,0.5), rgba(0,0,0,0.85)), url('<?php echo htmlspecialchars($backgroundImage, ENT_QUOTES, 'UTF-8'); ?>');
            background-attachment: fixed;
            background-position: center bottom;
            background-size: cover;
            background-repeat: no-repeat;
            position: relative;
            padding: 80px 24px 100px;
            overflow: hidden;
        }

        .hero-section::before, .hero-section::after {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.5;
            z-index: 1;
        }

        .hero-section::before {
            background: var(--accent);
            top: -120px;
            left: -120px;
        }

        .hero-section::after {
            background: #ff2d55;
            bottom: -160px;
            right: -80px;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 24px;
            max-width: 960px;
            width: 100%;
            backdrop-filter: blur(6px);
        }

        .badge {
            background: rgba(0,0,0,0.5);
            color: #ffe7cf;
            padding: 7px 18px;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 700;
            display: inline-flex;
            gap: 8px;
            align-items: center;
            letter-spacing: 0.4px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .main-title {
            font-size: clamp(3.8rem, 8vw, 7rem);
            line-height: 0.9;
            margin: 14px 0 12px 0;
            animation: pulse 3s ease-in-out infinite;
            text-shadow: 0 8px 25px rgba(0,0,0,0.6);
        }

        .sub-title {
            color: #ffe6cd;
            font-size: 1.4rem;
            margin-bottom: 22px;
            font-weight: 400;
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
        }

        .hero-badges {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin: 10px 0 26px 0;
        }

        .floating-badge {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 10px 18px;
            border-radius: 12px;
            color: #ffe7cf;
            font-weight: 700;
            letter-spacing: 0.3px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.35);
            backdrop-filter: blur(6px);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .floating-badge:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.45);
        }

        .cta-button {
            padding: 14px 28px;
            font-size: 1.05rem;
            font-weight: 800;
            background: linear-gradient(120deg, var(--accent), #ff8727);
            color: white;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(255, 111, 0, 0.35);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(255, 111, 0, 0.45);
        }

        /* --- SECÇÃO DE EMENTA SEMANAL --- */
        .menu-reveal {
            position: relative;
            background: radial-gradient(circle at 20% 20%, #fff3e0, var(--cream));
            min-height: 100vh;
            padding: 120px 20px 70px;
            box-shadow: 0 -30px 60px rgba(0,0,0,0.7);
            border-top: 14px solid var(--accent);
            border-radius: 36px 36px 0 0;
            margin-top: -60px;
            z-index: 5;
        }

        .menu-reveal::before {
            content: "";
            position: absolute;
            inset: 40px 30px;
            background: repeating-linear-gradient(
                -45deg,
                rgba(255, 111, 0, 0.05),
                rgba(255, 111, 0, 0.05) 12px,
                transparent 12px,
                transparent 24px
            );
            border-radius: 28px;
            z-index: 0;
        }

        .section-title {
            font-size: clamp(2.6rem, 6vw, 3.8rem);
            text-align: center;
            margin-bottom: 8px;
            color: var(--ink);
        }

        .menu-intro {
            text-align: center;
            color: #6a5038;
            max-width: 720px;
            margin: 0 auto 36px;
            font-style: italic;
            font-weight: 600;
            line-height: 1.5;
        }

        .menu-highlight {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 18px auto 50px;
        }

        .menu-highlight span {
            background: #ffe2c2;
            color: #4a2a10;
            padding: 8px 14px;
            border-radius: 10px;
            font-weight: 800;
            letter-spacing: 0.3px;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 10px 18px rgba(0,0,0,0.06);
        }

        .weekly-menu-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 22px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .weekly-menu-item {
            background: #fff;
            border-radius: 18px;
            padding: 22px 20px 24px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
            border: 2px solid #f4d7b6;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease;
            text-align: left;
            position: relative;
        }

        .weekly-menu-item::after {
            content: "Kina";
            position: absolute;
            top: 16px;
            right: 18px;
            font-family: 'Chewy', cursive;
            color: rgba(0,0,0,0.1);
            font-size: 1.5rem;
        }

        .weekly-menu-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 35px rgba(255, 111, 0, 0.18);
        }

        .day-of-week {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .dish-name {
            font-size: 1.3rem;
            font-family: 'Chewy', cursive;
            color: #2c1a0d;
            line-height: 1.25;
            min-height: 40px;
        }

        .date-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #ffefe0;
            color: #4a2a10;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid #ffd9b3;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.5), 0 8px 18px rgba(0,0,0,0.05);
            font-weight: 800;
        }

        .drip {
            position: absolute;
            top: -45px;
            left: 0;
            width: 100%;
            height: 60px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ff6f00' fill-opacity='1' d='M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            background-repeat: no-repeat;
            transform: rotate(180deg);
            z-index: 6;
            pointer-events: none;
        }

        footer {
            background: #0c0a08;
            color: #d9c8b6;
            text-align: center;
            padding: 46px 20px 52px;
            margin-top: auto;
            border-top: 5px solid var(--accent);
            position: relative;
            z-index: 2;
        }

        footer strong { color: #fff; }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .main-title { font-size: 3.8rem; }
            .section-title { font-size: 2.4rem; }
            .weekly-menu-grid { grid-template-columns: 1fr; }
            .hero-section { background-attachment: scroll; }
        }
    </style>
</head>
<body>

    <header class="hero-section">
        <div class="hero-content">
            <div class="badge">Casa da Francesinha</div>
            <h1 class="sauce-text main-title">Kafé Kina</h1>
            <p class="sub-title">A francesinha que faz Olival sorrir.</p>
            <div class="hero-badges">
                <span class="floating-badge">Casa da Francesinha</span>
                <span class="floating-badge">Molho da Casa</span>
                <span class="floating-badge">Conforto em forma de francesinha</span>
                <span class="floating-badge">Simples, honesto, delicioso</span>
            </div>
            <a href="#ementa-semanal" class="cta-button">Passar à ementa</a>
        </div>
    </header>

    <section id="ementa-semanal" class="menu-reveal">
        <div class="drip"></div>

        <div class="menu-head" style="text-align: center; position: relative; z-index: 1;">
            <h2 class="sauce-text section-title">Ementa da Semana</h2>
            <p class="menu-intro">A melhor francesinha da região. Molho da Casa, pão estaladiço e uma mesa que sabe sempre bem.</p>
            <div class="menu-highlight">
                <span>Casa da Francesinha</span>
                <span>Molho da Casa</span>
                <span>Conforto em forma de francesinha</span>
                <span>Simples, honesto, delicioso</span>
            </div>
            <?php if ($rangeText !== ''): ?>
                <div style="margin-bottom: 30px;">
                    <span class="date-pill">Semana: <?php echo htmlspecialchars($rangeText, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="weekly-menu-grid">
            <?php foreach ($daysOfWeek as $day):
                $dish = $menuData['menu'][$day] ?? '';
                $fallbackDish = $dish !== '' ? $dish : 'Por definir';
                $isFrancesinha = stripos($fallbackDish, 'francesinha') !== false;
                $dishClass = $isFrancesinha ? ' sauce-text' : '';
            ?>
            <div class="weekly-menu-item">
                <span class="day-of-week"><?php echo htmlspecialchars($day, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="dish-name<?php echo $dishClass; ?>"><?php echo htmlspecialchars($fallbackDish, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer>
        <p><strong>Kafé Kina</strong> | Rua Central do Olival 4162, Vila Nova de Gaia</p>
        <p style="margin-top: 10px; font-size: 0.9rem;">Tel: 965 659 041 | Aberto todos os dias (exceto Domingo)</p>
    </footer>

    <script>
        // Scroll Suave
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
