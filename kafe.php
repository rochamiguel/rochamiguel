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
        /* --- RESET & GERAL --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Rubik', sans-serif;
            background-color: #1a1a1a;
            overflow-x: hidden;
        }

        /* --- TIPOGRAFIA "MOLHO KINA" --- */
        .sauce-text {
            font-family: 'Chewy', cursive;
            color: #ff6f00; /* Laranja Molho */
            text-transform: uppercase;
            letter-spacing: 2px;
            
            /* Efeito 3D Líquido */
            text-shadow: 
                4px 4px 0px #b33e00,
                6px 6px 15px rgba(0,0,0,0.6),
                inset 2px 2px 5px rgba(255, 255, 255, 0.5);
            
            position: relative;
            z-index: 10;
        }

        /* --- EFEITO PARALLAX (FRANCESINHA NO FUNDO) --- */
        .hero-section {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;

            /* Imagem configurável da francesinha */
            background-image: url('<?php echo htmlspecialchars($backgroundImage, ENT_QUOTES, 'UTF-8'); ?>');

            background-attachment: fixed; 
            background-position: center bottom; /* Ajustado para a francesinha ficar mais visível */
            background-size: cover;
            background-repeat: no-repeat;
            
            position: relative;
        }

        /* Overlay escuro para destacar o nome Kina */
        .overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.85));
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 20px;
        }

        .main-title {
            font-size: 7rem;
            line-height: 0.9;
            margin-bottom: 10px;
            animation: pulse 3s ease-in-out infinite;
        }

        .sub-title {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 30px;
            font-weight: 300;
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
        }

        .badge {
            background: #d32f2f;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .cta-button {
            padding: 15px 50px;
            font-size: 1.6rem;
            font-family: 'Chewy', cursive;
            background: #ff6f00;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 5px 0 #b33e00, 0 10px 20px rgba(0,0,0,0.4);
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .cta-button:hover {
            transform: translateY(4px);
            box-shadow: 0 1px 0 #b33e00, 0 5px 10px rgba(0,0,0,0.4);
            background: #ff8c00;
        }

        /* --- SECÇÃO DE EMENTA SEMANAL --- */
        .menu-reveal {
            position: relative;
            background-color: #fffcf5;
            min-height: 100vh;
            padding: 100px 20px 50px;
            box-shadow: 0 -30px 60px rgba(0,0,0,0.7);
            border-top: 12px solid #ff6f00;
            border-radius: 40px 40px 0 0;
            margin-top: -60px; /* Efeito de sobreposição */
            z-index: 5;
        }

        .section-title {
            font-size: 3.5rem;
            text-align: center;
            margin-bottom: 10px;
        }

        .menu-intro {
            text-align: center;
            color: #666;
            max-width: 600px;
            margin: 0 auto 60px;
            font-style: italic;
        }

        .weekly-menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Adaptado para 6 dias */
            gap: 25px;
            max-width: 900px; /* Mais compacto */
            margin: 0 auto;
        }

        .weekly-menu-item {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 15px rgba(0,0,0,0.08);
            transition: transform 0.2s ease-in-out;
            text-align: center;
            border-bottom: 4px solid #d32f2f;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .weekly-menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(255, 111, 0, 0.15);
        }

        .day-of-week {
            font-size: 1.3rem;
            font-weight: 700;
            color: #ff6f00;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .dish-name {
            font-size: 1.6rem;
            font-family: 'Chewy', cursive;
            color: #222;
            line-height: 1.2;
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

        /* Footer */
        footer {
            background: #222;
            color: #bbb;
            text-align: center;
            padding: 40px 20px;
            margin-top: auto;
            border-top: 5px solid #d32f2f;
        }
        
        footer strong { color: white; }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .main-title { font-size: 4.5rem; }
            .section-title { font-size: 2.5rem; }
            .weekly-menu-grid { grid-template-columns: 1fr; } /* Uma coluna em mobile */
        }
    </style>
</head>
<body>

    <header class="hero-section">
        <div class="overlay"></div>
        <div class="hero-content">
            <!-- <div class="badge">Desde sempre em Olival</div> -->
            <h1 class="sauce-text main-title">Kafé Kina</h1>
            <!-- <p class="sub-title">A lenda da Francesinha, o sabor da tradição.</p> -->
            <a href="#ementa-semanal" class="cta-button">Ver Ementa Semanal</a>
        </div>
    </header>

    <section id="ementa-semanal" class="menu-reveal">
        <div class="drip"></div>

        <h2 class="sauce-text section-title">A Nossa Ementa da Semana</h2>

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
        <?php if ($rangeText !== ''): ?>
            <p style="text-align: center; margin-top: 40px; color: #555;">(Ementa válida de <?php echo htmlspecialchars($rangeText, ENT_QUOTES, 'UTF-8'); ?>)</p>
        <?php endif; ?>
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
