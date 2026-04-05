<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Addiction Population — AddictData</title>
    <link rel="stylesheet" href="styles/style_general.css">
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
                <li><a href="index.php#presentation">Projet</a></li>
                <li><a href="index.php#datasets">Jeux de données</a></li>
                <li><a href="index.php#equipe">Équipe</a></li>
            </ul>
            <span class="nav-badge">Projet Étudiant 2024–2025</span>
        </div>
    </nav>

    <!-- ── HERO ── -->
    <header class="dataset-hero">
        <div class="hero-grid-lines" aria-hidden="true">
            <?php for ($i = 0; $i < 6; $i++): ?>
            <div class="grid-line"></div>
            <?php endfor; ?>
        </div>

        <div class="dataset-hero-inner">
            <div class="dataset-breadcrumb">
                <a href="index.php">Accueil</a>
                <span class="breadcrumb-sep">/</span>
                <a href="index.php#datasets">Jeux de données</a>
                <span class="breadcrumb-sep">/</span>
                <span>Addiction Population</span>
            </div>

            <div class="dataset-meta-row">
                <span class="dataset-icon-badge">🎲</span>
                <span class="dataset-tag-id">04 · Gambling Addiction</span>
            </div>

            <h1 class="dataset-hero-title">
                Addiction<br><em>population alcool</em>
            </h1>

            <p class="dataset-hero-sub">
                Profil socio-économique des joueurs compulsifs et facteurs de risque 
                associés à la dépendance. Une analyse de 3 056 observations pour 
                mieux comprendre les comportements addictifs liés aux jeux de hasard.
            </p>

            <div class="dataset-hero-pills">
                <span class="hero-pill"><span class="hero-pill-dot"></span>3 056 entrées</span>
                <span class="hero-pill"><span class="hero-pill-dot"></span>Économie &amp; Risque</span>
                <span class="hero-pill"><span class="hero-pill-dot"></span>Comportement</span>
                <span class="hero-pill"><span class="hero-pill-dot"></span>Données quantitatives</span>
            </div>
        </div>
    </header>

    <!-- ── MAIN CONTENT ── -->
    <main>
        <div class="page-content">

            <a href="accueil_v2.php" class="back-link reveal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Retour aux jeux de données
            </a>

            <div class="stats-bar reveal">
                <div class="stat-box">
                    <span class="stat-box-num">3 056</span>
                    <span class="stat-box-label">Observations</span>
                </div>
                <div class="stat-box">
                    <span class="stat-box-num">18+</span>
                    <span class="stat-box-label">Tranches d'âge</span>
                </div>
                <div class="stat-box">
                    <span class="stat-box-num">12</span>
                    <span class="stat-box-label">Variables</span>
                </div>
                <div class="stat-box">
                    <span class="stat-box-num">4</span>
                    <span class="stat-box-label">Modèles ML</span>
                </div>
            </div>

            <div class="content-block reveal">
                <div class="block-label">
                    <span class="block-label-line"></span>
                    À propos du dataset
                </div>
                <h2 class="block-title">Comprendre la dépendance<br>aux jeux de hasard.</h2>
                <p class="block-text">
                    Ce jeu de données explore les <strong>profils socio-économiques</strong> de personnes 
                    présentant une dépendance aux jeux de hasard et à l'alcool au sein d'une population générale. 
                    Il regroupe <strong>3 056 entrées</strong> issues de différentes tranches d'âge et catégories sociales.
                </p>
                <p class="block-text">
                    L'objectif est d'identifier les <strong>facteurs de risque</strong> associés aux comportements 
                    addictifs — revenus, niveau d'éducation, environnement social — et de construire des modèles 
                    prédictifs capables de détecter des profils vulnérables.
                </p>
                <div class="info-grid">
                    <div class="info-card">
                        <p class="info-card-title">📊 Variables analysées</p>
                        <p class="info-card-text">Âge, genre, niveau d'éducation, revenus, fréquence de consommation d'alcool, comportement de jeu, score d'addiction et facteurs environnementaux.</p>
                    </div>
                    <div class="info-card">
                        <p class="info-card-title">🎯 Variable cible</p>
                        <p class="info-card-text">Classification binaire ou multi-classes du niveau d'addiction (faible / modéré / sévère) à partir des variables comportementales et socio-économiques.</p>
                    </div>
                    <div class="info-card">
                        <p class="info-card-title">🔬 Méthodes utilisées</p>
                        <p class="info-card-text">Statistiques descriptives, corrélations, visualisations, modèles KNN, Naive Bayes, XGBoost et comparaison des performances.</p>
                    </div>
                    <div class="info-card">
                        <p class="info-card-title">📁 Source des données</p>
                        <p class="info-card-text">Dataset issu de sources épidémiologiques publiques, utilisé à des fins strictement pédagogiques dans le cadre du projet AddictData.</p>
                    </div>
                </div>
            </div>

            <div class="content-block reveal">
                <div class="block-label">
                    <span class="block-label-line"></span>
                    Passer à l'action
                </div>
                <div class="quiz-cta">
                    <h2 class="quiz-cta-title">Évalue ton niveau d'addiction</h2>
                    <p class="quiz-cta-sub">
                        Réponds à quelques questions pour obtenir une estimation de ton profil 
                        et mieux comprendre tes comportements.
                    </p>
                    <div class="hero-cta" style="justify-content: center;">
                        <a href="quiz.php" class="btn btn-violet">
                            Effectue un test
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                        <a href="informations.php" class="btn btn-ghost-violet">S'informer</a>
                    </div>
                </div>
            </div>

        </div>
    </main>

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
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>

</body>
</html>