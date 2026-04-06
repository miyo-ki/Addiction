<?php
$datasets = [
    [
        "id" => 1,
        "slug" => "social-addiction",
        "title" => "Addiction aux Réseaux Sociaux",
        "subtitle" => "Social Addiction",
        "description" => "Analyse comportementale de l'usage excessif des plateformes numériques et son impact sur la santé mentale.",
        "entries" => "705 entrées",
        "tags" => ["Comportement", "Numérique", "Santé Mentale"],
        "icon" => "💬",
        "file" => "social_addiction.php"
    ],
    [
        "id" => 2,
        "slug" => "Student-mat",
        "title" => "Addiction à l'alcool des étudiants",
        "subtitle" => "Alcool Addiction",
        "description" => "Étude sur la consommation d'alcool chez les étudiants.",
        "entries" => "395 entrées",
        "tags" => ["Épidémiologie", "Alcool", "Étudiants"],
        "icon" => "🍺",
        "file" => "student-mat.php"
    ],
    [
        "id" => 3,
        "slug" => "mobile_addiction",
        "title" => "Addiction aux téléphones mobiles",
        "subtitle" => "Mobile Addiction",
        "description" => "Mesure du temps d'écran, des patterns d'usage et des corrélations avec l'isolement social.",
        "entries" => "3 000 entrées",
        "tags" => ["Temps d'écran", "Santé Mentale", "Numérique"],
        "icon" => "📱",
        "file" => "mobile_addiction.php"
    ],
    [
        "id" => 4,
        "slug" => "addiction_population",
        "title" => "Addiction aux cigarettes",
        "subtitle" => "Smoke Addiction",
        "description" => "Analyse des tendances de consommation de tabac, des facteurs socio-économiques et des risques associés.",
        "entries" => "3 000 entrées",
        "tags" => ["Tabagisme", "Socio-économique", "Santé physique"],
        "icon" => "🚬",
        "file" => "addiction_population.php"
    ]
];

$members = [
    ["name" => "Lea Carminati"],
    ["name" => "Mona Bourgeron"],
    ["name" => "Lana Schembri"],
    ["name" => "Sidney Dachez"],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AddictData</title>
    <link rel="stylesheet" href="styles/style_general.css">
</head>
<body>

    <!-- ── NOISE OVERLAY ── -->
    <div class="noise"></div>

    <!-- ── NAVIGATION ── -->
    <nav class="navbar">
        <div class="nav-inner">
            <a href="index.php" class="nav-logo">
                <span class="logo-text">AddictData</span>
            </a>
            <ul class="nav-links">
                <li><a href="#presentation">Projet</a></li>
                <li><a href="#datasets">Jeux de données</a></li>
                <li><a href="#equipe">Équipe</a></li>
            </ul>
        </div>
    </nav>

    <!-- ── HERO ── -->
    <header class="hero" id="hero">

        <div class="hero-content">
            <p class="hero-eyebrow">
                <span class="dot"></span> Analyse de données · L3 MIASHS Université Paul Valéry Montpellier · 2025–2026
            </p>
            <h1 class="hero-title">
                Comprendre<br>
                <em>l'addiction</em><br>
                par les données.
            </h1>
            <p class="hero-sub">
                Quatre jeux de données. Quatre formes de dépendance.<br>
                Une approche scientifique pour mieux comprendre un enjeu de société.
                <BR>Besoin d'aide ? Cliquez <a href="https://www.ameli.fr/assure/sante/themes/addictions/suivi">ICI</a>.
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
                <span class="stat-num">7 100</span>
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
                        Dans le cadre de notre formation en licence de <strong>MIASHS</strong> (Mathématique et Informatique Appliquées aux Sciences Humaines et Sociales) de l'université Paul Valéry Montpellier, 
                        nous avons choisi d'explorer un sujet aux ramifications médicales, sociales et économiques 
                        profondes&nbsp;: <strong>l'addiction</strong>.
                    </p>
                    <p class="pres-text">
                        Ce projet analyse quatre types de dépendance — aux réseaux sociaux, à l'alcool chez les étudiants, aux téléphones portables et à la cigarette  — à travers des techniques de 
                        <strong>statistiques descriptives</strong>, de <strong>visualisation</strong> et de 
                        <strong>modélisation prédictive</strong>.
                        Cependant, les différents jeux de données, à l'exception de celui portant sur l'addiction aux réseaux sociaux, ont été générés de manière aléatoire. De ce fait, ce projet a une vocation purement pédagogique et ne prétend pas à une quelconque validité scientifique.
                    </p>
                    <p class="pres-text">
                        Notre objectif&nbsp;: identifier des patterns comportementaux, des facteurs de risque 
                        et proposer des représentations visuelles exploitables pour sensibiliser à ces problématiques.
                    </p>

                    <div class="pres-methods">
                        <?php foreach (["Statistiques descriptives", "Corrélations", "Visualisation", "Modélisation", "Prédiction"] as $m): ?>
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
                Projet universitaire — L3 MIASHS Université Paul Valéry Montpellier &nbsp;·&nbsp; 2025–2026 &nbsp;·&nbsp; 
                Données à usage strictement pédagogique.
            </p>
        </div>
    </footer>
</body>
</html>