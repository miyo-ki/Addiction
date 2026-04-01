<?php
$datasets = [
    [
        "id" => 1,
        "slug" => "social-addiction",
        "title" => "Addiction aux Réseaux Sociaux",
        "subtitle" => "Social Addiction",
        "description" => "Analyse comportementale de l'usage excessif des plateformes numériques et son impact sur la santé mentale.",
        "entries" => "2 341 entrées",
        "tags" => ["Comportement", "Numérique", "Santé Mentale"],
        "icon" => "📱",
        "color" => "amber",
        "file" => "social_addiction.php"
    ],
    [
        "id" => 2,
        "slug" => "substances",
        "title" => "Addiction à l'alcool des étudiants, avec notes en math",
        "subtitle" => "Student-mat",
        "description" => "Étude épidémiologique sur la consommation d'alcool, tabac et drogues dans différentes tranches d'âge.",
        "entries" => "4 812 entrées",
        "tags" => ["Épidémiologie", "Substances", "Âge"],
        "icon" => "⚗️",
        "color" => "rose",
        "file" => "student-mat.php"
    ],
    [
        "id" => 3,
        "slug" => "jeux-video",
        "title" => "Addiction aux mobiles",
        "subtitle" => "mobile-addiction",
        "description" => "Mesure du temps de jeu, des patterns d'usage et des corrélations avec l'isolement social.",
        "entries" => "1 678 entrées",
        "tags" => ["Gaming", "Isolement", "Jeunes"],
        "icon" => "🎮",
        "color" => "cyan",
        "file" => "mobile_addiction.php"
    ],
    [
        "id" => 4,
        "slug" => "jeux-hasard",
        "title" => "Addiction population alcool",
        "subtitle" => "Gambling Addiction",
        "description" => "Profil socio-économique des joueurs compulsifs et facteurs de risque associés à la dépendance.",
        "entries" => "3 056 entrées",
        "tags" => ["Économie", "Risque", "Comportement"],
        "icon" => "🎲",
        "color" => "violet",
        "file" => "addiction_population.php"
    ]
];

$members = [
    ["name" => "Alice Martin", "role" => "Chef de projet & Data Analyst"],
    ["name" => "Baptiste Nguyen", "role" => "Développeur & Visualisation"],
    ["name" => "Clara Dufresne", "role" => "Statisticienne"],
    ["name" => "David Okafor", "role" => "Rédaction & Recherche"],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AddictData — Analyse de l'Addiction</title>
    <link rel="stylesheet" href="styles/style_v2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&family=Lora:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
</head>
<body>

    <!-- ── NOISE OVERLAY ── -->
    <div class="noise"></div>

    <!-- ── NAVIGATION ── -->
    <nav class="navbar">
        <div class="nav-inner">
            <a href="index.php" class="nav-logo">
                <span class="logo-mark">A</span>
                <span class="logo-text">ddictData</span>
            </a>
            <ul class="nav-links">
                <li><a href="#presentation">Projet</a></li>
                <li><a href="#datasets">Jeux de données</a></li>
                <li><a href="#equipe">Équipe</a></li>
            </ul>
            <span class="nav-badge">Projet Étudiant 2024–2025</span>
        </div>
    </nav>

    <!-- ── HERO ── -->
    <header class="hero" id="hero">
        <div class="hero-grid-lines" aria-hidden="true">
            <?php for ($i = 0; $i < 6; $i++): ?>
            <div class="grid-line"></div>
            <?php endfor; ?>
        </div>
        <div class="hero-content">
            <p class="hero-eyebrow">
                <span class="dot"></span> Analyse de données · IUT Informatique · 2024–2025
            </p>
            <h1 class="hero-title">
                Comprendre<br>
                <em>l'addiction</em><br>
                par les données.
            </h1>
            <p class="hero-sub">
                Quatre jeux de données. Quatre formes de dépendance.<br>
                Une approche scientifique pour mieux comprendre un enjeu de société.
            </p>
            <div class="hero-cta">
                <a href="#datasets" class="btn btn-primary">Explorer les données</a>
                <a href="#presentation" class="btn btn-ghost">En savoir plus</a>
            </div>
        </div>
        <div class="hero-stats" aria-hidden="true">
            <div class="stat-pill">
                <span class="stat-num">4</span>
                <span class="stat-label">Jeux de données</span>
            </div>
            <div class="stat-pill">
                <span class="stat-num">11 887</span>
                <span class="stat-label">Observations</span>
            </div>
            <div class="stat-pill">
                <span class="stat-num">4</span>
                <span class="stat-label">Membres</span>
            </div>
        </div>
        <div class="hero-scroll-hint" aria-hidden="true">
            <span>Défiler</span>
            <div class="scroll-line"></div>
        </div>
    </header>

    <!-- ── PRÉSENTATION ── -->
    <section class="presentation" id="presentation">
        <div class="section-inner">
            <div class="section-label">
                <span class="label-line"></span>
                <span>Notre Projet</span>
            </div>
            <div class="pres-layout">
                <div class="pres-left">
                    <h2 class="section-title">Une étude<br>pluridisciplinaire<br>de la dépendance.</h2>
                </div>
                <div class="pres-right">
                    <p class="pres-text">
                        Dans le cadre de notre formation en <strong>informatique et science des données</strong>, 
                        nous avons choisi d'explorer un sujet aux ramifications médicales, sociales et économiques 
                        profondes&nbsp;: <strong>l'addiction</strong>.
                    </p>
                    <p class="pres-text">
                        Ce projet analyse quatre types de dépendance — aux réseaux sociaux, aux substances 
                        psychoactives, aux jeux vidéo et aux jeux de hasard — à travers des techniques de 
                        <strong>statistiques descriptives</strong>, de <strong>visualisation</strong> et de 
                        <strong>modélisation prédictive</strong>.
                    </p>
                    <p class="pres-text">
                        Notre objectif&nbsp;: identifier des patterns comportementaux, des facteurs de risque 
                        et proposer des représentations visuelles exploitables pour sensibiliser à ces problématiques.
                    </p>
                    <div class="pres-methods">
                        <?php foreach (["Statistiques descriptives", "Corrélations", "Visualisation", "Modélisation", "Clustering"] as $m): ?>
                        <span class="method-tag"><?= $m ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── DATASETS ── -->
    <section class="datasets" id="datasets">
        <div class="section-inner">
            <div class="section-label">
                <span class="label-line"></span>
                <span>Jeux de Données</span>
            </div>
            <h2 class="section-title section-title--center">Quatre analyses,<br>un seul regard.</h2>

            <div class="cards-grid">
                <?php foreach ($datasets as $ds): ?>
                <article class="card card--<?= $ds['color'] ?>">
                    <div class="card-header">
                        <span class="card-number">0<?= $ds['id'] ?></span>
                        <span class="card-icon"><?= $ds['icon'] ?></span>
                    </div>
                    <div class="card-body">
                        <p class="card-subtitle"><?= $ds['subtitle'] ?></p>
                        <h3 class="card-title"><?= $ds['title'] ?></h3>
                        <p class="card-desc"><?= $ds['description'] ?></p>
                        <div class="card-tags">
                            <?php foreach ($ds['tags'] as $tag): ?>
                            <span class="tag"><?= $tag ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <span class="card-entries"><?= $ds['entries'] ?></span>
                        <a href="<?= $ds['file'] ?>" class="card-link">
                            Analyser
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ── ÉQUIPE ── -->
    <section class="equipe" id="equipe">
        <div class="section-inner">
            <div class="section-label">
                <span class="label-line"></span>
                <span>L'Équipe</span>
            </div>
            <h2 class="section-title">Qui sommes-nous&nbsp;?</h2>
            <div class="team-grid">
                <?php foreach ($members as $i => $m): ?>
                <div class="team-card">
                    <div class="team-avatar">
                        <span><?= mb_substr($m['name'], 0, 1) ?></span>
                    </div>
                    <div class="team-info">
                        <p class="team-name"><?= $m['name'] ?></p>
                        <p class="team-role"><?= $m['role'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ── FOOTER ── -->
    <footer class="footer">
        <div class="footer-inner">
            <p class="footer-logo">AddictData</p>
            <p class="footer-copy">
                Projet universitaire — IUT Informatique &nbsp;·&nbsp; 2024–2025 &nbsp;·&nbsp; 
                Données à usage strictement pédagogique.
            </p>
        </div>
    </footer>

    <script>
        // Smooth scroll reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('.card, .team-card, .pres-right, .pres-left, .hero-stats').forEach(el => {
            el.classList.add('reveal');
            observer.observe(el);
        });

        // Active nav on scroll
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-links a');
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(s => {
                if (window.scrollY >= s.offsetTop - 120) current = s.getAttribute('id');
            });
            navLinks.forEach(a => {
                a.classList.toggle('active', a.getAttribute('href') === '#' + current);
            });
        });
    </script>
</body>
</html>