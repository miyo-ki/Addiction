<?php
/* ══════════════════════════════════════════
   mobile_addiction.php
   Gère aussi l'appel AJAX de prédiction
   ══════════════════════════════════════════ */

// ── AJAX : prédiction ──────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'predict') {
    header('Content-Type: application/json');

    $fields = [
        'age'          => FILTER_VALIDATE_INT,
        'gender'       => FILTER_SANITIZE_SPECIAL_CHARS,
        'occupation'   => FILTER_SANITIZE_SPECIAL_CHARS,
        'education'    => FILTER_SANITIZE_SPECIAL_CHARS,
        'screen_time'  => FILTER_VALIDATE_FLOAT,
        'unlocks'      => FILTER_VALIDATE_INT,
        'social_hours' => FILTER_VALIDATE_FLOAT,
        'sleep_hours'  => FILTER_VALIDATE_FLOAT,
        'mental_health'=> FILTER_VALIDATE_INT,
        'stress'       => FILTER_VALIDATE_INT,
        'first_phone'  => FILTER_VALIDATE_INT,
        'has_app'      => FILTER_SANITIZE_SPECIAL_CHARS,
        'physical'     => FILTER_VALIDATE_FLOAT,
    ];

    $data = [];
    foreach ($fields as $k => $filter) {
        $val = filter_input(INPUT_POST, $k, $filter);
        if ($val === false || $val === null) {
            echo json_encode(['error' => "Champ invalide : $k"]);
            exit;
        }
        $data[$k] = $val;
    }

    $predict_script = __DIR__ . '/predict_mobile.py';
    $python         = 'python3';

    $args = implode(' ', [
        escapeshellarg((string)$data['age']),
        escapeshellarg($data['gender']),
        escapeshellarg($data['occupation']),
        escapeshellarg($data['education']),
        escapeshellarg((string)$data['screen_time']),
        escapeshellarg((string)$data['unlocks']),
        escapeshellarg((string)$data['social_hours']),
        escapeshellarg((string)$data['sleep_hours']),
        escapeshellarg((string)$data['mental_health']),
        escapeshellarg((string)$data['stress']),
        escapeshellarg((string)$data['first_phone']),
        escapeshellarg($data['has_app']),
        escapeshellarg((string)$data['physical']),
    ]);

    $cmd    = "$python -W ignore " . escapeshellarg($predict_script) . " $args 2>&1";
    $output = trim(shell_exec($cmd));

    $lines  = explode("\n", $output);
    $last   = trim(end($lines));

    $result = json_decode($last, true);
    if (!$result) {
        echo json_encode(['error' => 'Erreur du modèle', 'raw' => $output]);
    } else {
        if (isset($result['erreur'])) {
            echo json_encode(['error' => $result['erreur']]);
        } else {
            echo json_encode($result);
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Addiction — AddictData</title>
    <link rel="stylesheet" href="styles/style_general.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&family=Lora:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <link rel="stylesheet" href="styles/style_social_addiction.css">
    <style>
        /* ── Surcharges spécifiques mobile_addiction ── */
        .level-low      { background: rgba(22,163,74,.1);  color: #15803d; border: 1px solid rgba(22,163,74,.25); }
        .level-moderate { background: rgba(245,158,11,.1); color: #b45309; border: 1px solid rgba(245,158,11,.3); }
        .level-high     { background: rgba(239,68,68,.1);  color: #dc2626; border: 1px solid rgba(239,68,68,.25); }
        .level-severe   { background: rgba(124,58,237,.1); color: #7c3aed; border: 1px solid rgba(139,92,246,.3); }

        .result-level.low      { background: rgba(22,163,74,.1);  color: #15803d; }
        .result-level.moderate { background: rgba(245,158,11,.1); color: #b45309; }
        .result-level.high     { background: rgba(239,68,68,.1);  color: #dc2626; }
        .result-level.severe   { background: rgba(124,58,237,.1); color: #7c3aed; }

        .result-bar { background: linear-gradient(90deg, #16a34a 0%, #f59e0b 50%, #dc2626 80%, #7c3aed 100%); }

        .factors-list   { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .75rem; }
        .factor-tag     {
            font-family: var(--font-mono, 'DM Mono', monospace);
            font-size: .7rem; padding: 3px 10px; border-radius: 99px;
            background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.3);
            color: #92400e;
        }

        /* Range slider label */
        .range-row      { display: flex; align-items: center; gap: .6rem; }
        .range-row input[type=range] { flex: 1; accent-color: var(--accent, #f59e0b); }
        .range-val      { font-family: var(--font-mono, 'DM Mono', monospace); font-size: .75rem;
                          color: #f59e0b; min-width: 36px; text-align: right; }

        /* Résultats — facteurs contributeurs */
        .result-factors-section { margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid rgba(0,0,0,.07); }
        .result-factors-title   { font-family: var(--font-display, 'Syne', sans-serif);
                                   font-size: .9rem; font-weight: 700; color: var(--text, #1a1b23);
                                   margin-bottom: .5rem; }
    </style>
</head>
<body>

<div class="noise"></div>

<!-- ── NAVBAR ── -->
<nav class="navbar">
    <div class="nav-inner">
        <a href="accueil.php" class="nav-logo">
            <span class="logo-text">AddictData</span>
        </a>
        <ul class="nav-links">
            <li><a href="accueil_v2.php">Accueil</a></li>
            <li class="nav-dropdown">
                <a href="#">Datasets ▾</a>
                <ul class="nav-dropdown-menu">
                    <li><a href="social_addiction.php">Réseaux sociaux</a></li>
                    <li><a href="addiction_population.php">Addiction population</a></li>
                    <li><a href="mobile_addiction.php" class="active">Mobile addiction</a></li>
                    <li><a href="student-mat.php">Student performance</a></li>
                </ul>
            </li>
        </ul>
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
            <a href="accueil.php">Accueil</a>
            <span class="breadcrumb-sep">/</span>
            <a href="accueil_v2.php#datasets">Jeux de données</a>
            <span class="breadcrumb-sep">/</span>
            <span>Mobile Addiction</span>
        </div>
        <div class="dataset-meta-row">
            <span class="dataset-icon-badge">📱</span>
            <span class="dataset-tag-id">03 · Mobile Addiction</span>
        </div>
        <h1 class="dataset-hero-title">
            Students &amp;<br><em>Screen Addiction</em>
        </h1>
        <p class="dataset-hero-sub">
            Analyse des comportements d'usage mobile et prédiction du niveau d'addiction
            chez 5 000 étudiants à partir de données comportementales, socio-démographiques
            et de santé mentale.
        </p>
        <div class="dataset-hero-pills">
            <span class="hero-pill"><span class="hero-pill-dot"></span>5 000 observations</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>33 variables</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>Random Forest · MAE = 0.9634</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>4 niveaux d'addiction</span>
        </div>
    </div>
</header>

<!-- ── MAIN ── -->
<main>
<div class="page-content">

    <!-- Retour -->
    <a href="accueil_v2.php" class="back-link reveal">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Retour aux jeux de données
    </a>

    <!-- Stats bar -->
    <div class="stats-bar reveal">
        <div class="stat-box"><span class="stat-box-num">5 000</span><span class="stat-box-label">Observations</span></div>
        <div class="stat-box"><span class="stat-box-num">33</span><span class="stat-box-label">Variables</span></div>
        <div class="stat-box"><span class="stat-box-num">0.96</span><span class="stat-box-label">MAE meilleur modèle</span></div>
        <div class="stat-box"><span class="stat-box-num">4</span><span class="stat-box-label">Modèles testés</span></div>
    </div>

    <!-- ══════════════════════════════════════
         1. PRÉSENTATION DU DATASET
         ══════════════════════════════════════ -->
    <div class="content-block reveal" id="presentation">
        <div class="block-label"><span class="block-label-line"></span>Présentation du dataset</div>
        <h2 class="block-title">Students Mobile Screen Addiction</h2>
        <p class="block-text">
            Ce jeu de données recense <strong>5 000 individus</strong> (étudiants et jeunes actifs)
            et leur rapport à l'écran mobile, à travers des indicateurs comportementaux précis :
            temps passé sur les réseaux sociaux, fréquence des déverrouillages, durée de sommeil,
            scores de santé mentale et bien d'autres.
        </p>
        <p class="block-text">
            La <strong>variable cible</strong> <code>Addiction_Level</code> est un score catégoriel
            à quatre niveaux — <em>Low, Moderate, High, Severe</em> — construit à partir de
            l'ensemble des signaux comportementaux et psychologiques collectés.
        </p>

        <div class="info-grid">
            <div class="info-card">
                <p class="info-card-title">Source &amp; format</p>
                <p class="info-card-text">Fichier CSV tabulé (TSV), 5 000 lignes × 34 colonnes dont l'identifiant utilisateur. Variables numériques continues, ordinales et catégorielles textuelles.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">Objectif analytique</p>
                <p class="info-card-text">Prédire le niveau d'addiction à l'écran à partir des habitudes d'usage et du profil psycho-social, pour identifier les facteurs de risque les plus déterminants.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">Prétraitement</p>
                <p class="info-card-text">Suppression de <code>User_ID</code>, encodage LabelEncoder &amp; OneHotEncoder, normalisation StandardScaler, split 80/20 stratifié.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">Méthodes appliquées</p>
                <p class="info-card-text">KNN, Naive Bayes, XGBoost et Random Forest comparés sur deux pipelines d'encodage (LE et OHE). Optimisation par GridSearchCV sur le meilleur modèle.</p>
            </div>
        </div>

        <!-- Tableau des variables clés -->
        <div class="var-table-wrap" style="margin-top:2rem; overflow:hidden; border:1px solid rgba(0,0,0,.08); border-radius:12px;">
            <table style="width:100%; border-collapse:collapse; font-size:.85rem;">
                <thead style="background:rgba(245,158,11,.06);">
                    <tr>
                        <th style="padding:.75rem 1.1rem; text-align:left; font-family:var(--font-mono,'DM Mono',monospace); font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; color:#b45309; border-bottom:1px solid rgba(0,0,0,.07);">Variable</th>
                        <th style="padding:.75rem 1.1rem; text-align:left; font-family:var(--font-mono,'DM Mono',monospace); font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; color:#b45309; border-bottom:1px solid rgba(0,0,0,.07);">Type</th>
                        <th style="padding:.75rem 1.1rem; text-align:left; font-family:var(--font-mono,'DM Mono',monospace); font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; color:#b45309; border-bottom:1px solid rgba(0,0,0,.07);">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $variables = [
                        ['Daily_Screen_Time_Hours', 'Numérique', 'Temps d\'écran quotidien total (heures)'],
                        ['Social_Media_Usage_Hours','Numérique', 'Heures quotidiennes sur les réseaux sociaux'],
                        ['Phone_Unlocks_Per_Day',   'Numérique', 'Nombre de déverrouillages par jour'],
                        ['Sleep_Hours',             'Numérique', 'Durée de sommeil quotidienne'],
                        ['Mental_Health_Score',     'Numérique', 'Score global de santé mentale (0–20)'],
                        ['Depression_Score',        'Numérique', 'Score de dépression (PHQ-9)'],
                        ['Anxiety_Score',           'Numérique', 'Score d\'anxiété (GAD-7)'],
                        ['Stress_Level',            'Numérique', 'Niveau de stress ressenti (0–30)'],
                        ['Age',                     'Numérique', 'Âge de l\'individu'],
                        ['Age_First_Phone',         'Numérique', 'Âge au premier téléphone'],
                        ['Gender',                  'Catégoriel','Genre (Male / Female / Other)'],
                        ['Occupation',              'Catégoriel','Occupation (Student, Employed…)'],
                        ['Education_Level',         'Catégoriel','Niveau d\'éducation'],
                        ['Has_Screen_Time_Management_App','Catégoriel','Utilise une app de contrôle du temps'],
                        ['Physical_Activity_Hours', 'Numérique', 'Heures d\'activité physique quotidienne'],
                        ['Addiction_Level',         'Cible',     'Low / Moderate / High / Severe'],
                    ];
                    foreach ($variables as $i => $v):
                        $typeStyle = match($v[1]) {
                            'Numérique'  => 'background:rgba(6,182,212,.1);color:#0891b2;border:1px solid rgba(6,182,212,.25)',
                            'Catégoriel' => 'background:rgba(139,92,246,.1);color:#7c3aed;border:1px solid rgba(139,92,246,.25)',
                            'Cible'      => 'background:rgba(245,158,11,.1);color:#b45309;border:1px solid rgba(245,158,11,.3)',
                            default      => ''
                        };
                        $bg = $i % 2 === 0 ? '' : 'background:rgba(0,0,0,.015)';
                    ?>
                    <tr style="<?= $bg ?>">
                        <td style="padding:.6rem 1.1rem; font-family:var(--font-mono,'DM Mono',monospace); font-size:.78rem; color:#1a1b23; border-bottom:1px solid rgba(0,0,0,.04);"><?= $v[0] ?></td>
                        <td style="padding:.6rem 1.1rem; border-bottom:1px solid rgba(0,0,0,.04);">
                            <span style="font-family:var(--font-mono,'DM Mono',monospace); font-size:.68rem; padding:2px 8px; border-radius:99px; <?= $typeStyle ?>"><?= $v[1] ?></span>
                        </td>
                        <td style="padding:.6rem 1.1rem; color:#3d3f52; border-bottom:1px solid rgba(0,0,0,.04);"><?= $v[2] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         2. ANALYSES DESCRIPTIVES
         ══════════════════════════════════════ -->
    <div class="content-block reveal" id="analyses">
        <div class="analysis-section">
            <div class="block-label"><span class="block-label-line"></span>Analyses descriptives</div>
            <h2 class="block-title">Exploration des données</h2>
            <p class="analysis-intro">
                Avant de construire les modèles, nous avons analysé la distribution du niveau
                d'addiction, les corrélations avec le temps d'écran, l'impact de la santé mentale
                et le rôle de l'âge au premier téléphone.
            </p>

            <div class="charts-grid">

                <!-- Graphique 1 : Distribution addiction -->
                <div class="chart-card">
                    <p class="chart-title">Distribution du niveau d'addiction</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartDistribution"></canvas>
                    </div>
                </div>

                <!-- Graphique 2 : Temps d'écran par niveau -->
                <div class="chart-card">
                    <p class="chart-title">Temps d'écran selon le niveau d'addiction</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartScreenTime"></canvas>
                    </div>
                </div>

                <!-- Graphique 3 : Santé mentale (radar) -->
                <div class="chart-card">
                    <p class="chart-title">Scores de santé mentale — Low vs Severe</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartMental"></canvas>
                    </div>
                </div>

                <!-- Graphique 4 : Âge premier téléphone -->
                <div class="chart-card">
                    <p class="chart-title">Risque High/Severe par âge du 1er téléphone</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartFirstPhone"></canvas>
                    </div>
                </div>

                <!-- Graphique 5 : App gestion (full width) -->
                <div class="chart-card chart-card-full">
                    <p class="chart-title">Impact d'une app de gestion du temps d'écran</p>
                    <div class="chart-canvas-wrap-lg">
                        <canvas id="chartApp"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         3. MODÈLES IA
         ══════════════════════════════════════ -->
    <div class="content-block reveal" id="modele">
        <div class="block-label"><span class="block-label-line"></span>Modèles d'IA</div>
        <h2 class="block-title">Comparaison des modèles entraînés</h2>
        <p class="block-text">
            Quatre algorithmes comparés sur les mêmes splits train/test (80/20),
            avec deux stratégies d'encodage (LabelEncoder et OneHotEncoder).
            Les performances sont mesurées via <strong>R²</strong>, <strong>MAE</strong> et <strong>RMSE</strong>.
            Le meilleur modèle retenu est le <strong>Random Forest (LE)</strong> avec un MAE de 0.9634.
        </p>

        <div class="models-grid">
            <?php
            $models = [
                ['Random Forest', '-0.0047', '0.9634', '1.0889', true,  'LE'],
                ['XGBoost',       '-0.0081', '0.9646', '1.0907', false, 'LE'],
                ['Random Forest', '-0.0114', '0.9711', '1.0925', false, 'ACP'],
                ['Naive Bayes',   '-0.0151', '0.9623', '1.0945', false, 'LE'],
                ['XGBoost',       '-0.0169', '0.9742', '1.0955', false, 'ACP'],
                ['KNN',           '-0.0601', '0.9726', '1.1185', false, 'LE'],
                ['KNN',           '-0.0653', '0.9573', '1.1213', false, 'ACP'],
                ['Naive Bayes',   '-0.8684', '1.1650', '1.4849', false, 'ACP'],
            ];
            foreach ($models as [$name, $r2, $mae, $rmse, $best, $enc]):
            ?>
            <div class="model-card <?= $best ? 'best' : '' ?>">
                <?php if ($best): ?><span class="best-badge">★ Meilleur</span><?php endif; ?>
                <p class="model-name"><?= $name ?></p>
                <div class="model-metrics">
                    <div class="metric-row">
                        <span class="metric-label">R²</span>
                        <span class="metric-value <?= $best ? 'good' : '' ?>"><?= $r2 ?></span>
                    </div>
                    <div class="metric-bar-wrap">
                        <div class="metric-bar" style="width:<?= $best ? '96' : round((1 - abs((float)$r2)) * 100) ?>%"></div>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">MAE</span>
                        <span class="metric-value"><?= $mae ?></span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">RMSE</span>
                        <span class="metric-value"><?= $rmse ?></span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Encodage</span>
                        <span class="metric-value"><?= $enc ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="info-card" style="border-left:none;">
                <p class="info-card-title">Pourquoi Random Forest ?</p>
                <p class="info-card-text">
                    Le Random Forest construit <strong>plusieurs arbres de décision</strong> sur des
                    sous-ensembles aléatoires des données, puis agrège leurs prédictions par vote
                    majoritaire. Il est robuste au surapprentissage et capture efficacement les
                    interactions non-linéaires entre variables comportementales et psychologiques.
                </p>
            </div>
        </div>

        <!-- Explication en 3 étapes -->
        <div class="info-grid" style="margin-top:1.5rem;">
            <div class="info-card">
                <p class="info-card-title">01 · Bootstrap sampling</p>
                <p class="info-card-text">Chaque arbre est entraîné sur un sous-ensemble aléatoire des données, réduisant la variance du modèle global.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">02 · Feature randomness</p>
                <p class="info-card-text">À chaque nœud, seul un sous-ensemble de variables est considéré, forçant la diversité entre les 200 arbres.</p>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         4. PRÉDICTION
         ══════════════════════════════════════ -->
    <div class="content-block reveal" id="prediction">
        <div class="block-label"><span class="block-label-line"></span>Prédiction personnalisée</div>
        <h2 class="block-title">Estimez votre niveau d'addiction</h2>
        <p class="block-text">
            Renseignez votre profil ci-dessous. Le modèle <strong>Random Forest (LE)</strong>
            prédit votre niveau d'addiction parmi les 4 classes : Low, Moderate, High, Severe.
        </p>

        <div class="prediction-section">
            <form id="predictionForm">
                <div class="prediction-grid">

                    <!-- Âge -->
                    <div class="form-group">
                        <label class="form-label" for="age">Âge</label>
                        <input class="form-input" type="number" id="age" name="age"
                               min="10" max="80" value="20" required>
                    </div>

                    <!-- Genre -->
                    <div class="form-group">
                        <label class="form-label" for="gender">Genre</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="Male">Homme</option>
                            <option value="Female" selected>Femme</option>
                            <option value="Other">Autre</option>
                        </select>
                    </div>

                    <!-- Occupation -->
                    <div class="form-group">
                        <label class="form-label" for="occupation">Occupation</label>
                        <select class="form-select" id="occupation" name="occupation">
                            <option value="Student" selected>Étudiant</option>
                            <option value="Employed">Salarié</option>
                            <option value="Unemployed">Sans emploi</option>
                            <option value="Freelancer">Freelance</option>
                        </select>
                    </div>

                    <!-- Niveau d'éducation -->
                    <div class="form-group">
                        <label class="form-label" for="education">Niveau d'éducation</label>
                        <select class="form-select" id="education" name="education">
                            <option value="High School" selected>Lycée</option>
                            <option value="Undergraduate">Licence</option>
                            <option value="Graduate">Master</option>
                            <option value="Postgraduate">Doctorat</option>
                        </select>
                    </div>

                    <!-- Temps d'écran -->
                    <div class="form-group">
                        <label class="form-label" for="screen_time">Temps d'écran / jour</label>
                        <div class="range-row">
                            <input type="range" id="screen_time" name="screen_time"
                                   min="0" max="16" step="0.5" value="5"
                                   oninput="setVal('v_screen', this.value + 'h')">
                            <span class="range-val" id="v_screen">5h</span>
                        </div>
                    </div>

                    <!-- Déverrouillages -->
                    <div class="form-group">
                        <label class="form-label" for="unlocks">Déverrouillages / jour</label>
                        <div class="range-row">
                            <input type="range" id="unlocks" name="unlocks"
                                   min="5" max="200" step="5" value="50"
                                   oninput="setVal('v_unlocks', this.value)">
                            <span class="range-val" id="v_unlocks">50</span>
                        </div>
                    </div>

                    <!-- Réseaux sociaux -->
                    <div class="form-group">
                        <label class="form-label" for="social_hours">Réseaux sociaux / jour</label>
                        <div class="range-row">
                            <input type="range" id="social_hours" name="social_hours"
                                   min="0" max="12" step="0.5" value="3"
                                   oninput="setVal('v_social', this.value + 'h')">
                            <span class="range-val" id="v_social">3h</span>
                        </div>
                    </div>

                    <!-- Sommeil -->
                    <div class="form-group">
                        <label class="form-label" for="sleep_hours">Heures de sommeil</label>
                        <div class="range-row">
                            <input type="range" id="sleep_hours" name="sleep_hours"
                                   min="3" max="12" step="0.5" value="7"
                                   oninput="setVal('v_sleep', this.value + 'h')">
                            <span class="range-val" id="v_sleep">7h</span>
                        </div>
                    </div>

                    <!-- Santé mentale -->
                    <div class="form-group">
                        <label class="form-label" for="mental_health">Score santé mentale (0–20)</label>
                        <div class="range-row">
                            <input type="range" id="mental_health" name="mental_health"
                                   min="0" max="20" step="1" value="13"
                                   oninput="setVal('v_mental', this.value)">
                            <span class="range-val" id="v_mental">13</span>
                        </div>
                    </div>

                    <!-- Stress -->
                    <div class="form-group">
                        <label class="form-label" for="stress">Niveau de stress (0–30)</label>
                        <div class="range-row">
                            <input type="range" id="stress" name="stress"
                                   min="0" max="30" step="1" value="15"
                                   oninput="setVal('v_stress', this.value)">
                            <span class="range-val" id="v_stress">15</span>
                        </div>
                    </div>

                    <!-- Âge premier téléphone -->
                    <div class="form-group">
                        <label class="form-label" for="first_phone">Âge au 1er téléphone</label>
                        <input class="form-input" type="number" id="first_phone" name="first_phone"
                               min="5" max="25" value="13" required>
                    </div>

                    <!-- App gestion -->
                    <div class="form-group">
                        <label class="form-label" for="has_app">App de gestion du temps</label>
                        <select class="form-select" id="has_app" name="has_app">
                            <option value="No" selected>Non</option>
                            <option value="Yes">Oui</option>
                        </select>
                    </div>

                    <!-- Activité physique -->
                    <div class="form-group">
                        <label class="form-label" for="physical">Activité physique / jour</label>
                        <div class="range-row">
                            <input type="range" id="physical" name="physical"
                                   min="0" max="6" step="0.25" value="1"
                                   oninput="setVal('v_phys', this.value + 'h')">
                            <span class="range-val" id="v_phys">1h</span>
                        </div>
                    </div>

                </div><!-- /.prediction-grid -->

                <button type="submit" class="btn-predict">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                    Calculer mon niveau
                </button>
            </form>

            <!-- ── Résultat ── -->
            <div class="prediction-result" id="predictionResult">
                <div class="result-score" id="resultScore">—</div>
                <div class="result-label">Score prédit / 10</div>
                <div class="result-level" id="resultLevel">—</div>
                <div class="result-confidence" id="resultConfidence"></div>
                <div class="result-bar-wrap">
                    <div class="result-bar" id="resultBar"></div>
                </div>

                <!-- Facteurs contributeurs -->
                <div class="result-factors-section" id="resultFactorsSection" style="display:none;">
                    <div class="result-factors-title">Facteurs contributeurs identifiés</div>
                    <div class="factors-list" id="resultFactors"></div>
                </div>

                <!-- Positionnement dans la distribution -->
                <div class="result-distribution-wrap">
                    <p class="chart-title">Ton niveau dans la distribution générale</p>
                    <div class="result-dist-canvas">
                        <canvas id="chartResultPosition"></canvas>
                    </div>
                </div>

                <!-- Conseil si niveau élevé -->
                <div class="pro-advice" id="proAdvice">
                    <strong>Niveau élevé détecté !</strong>
                    Ton score d'addiction est significativement élevé. Il peut être utile d'en parler
                    à un professionnel de santé mentale (médecin, psychologue ou conseiller universitaire).
                    Des ressources comme <em>Santé Psy Étudiant</em> proposent des consultations gratuites.
                </div>
            </div>

            <!-- Erreur -->
            <div id="predictionError" style="display:none; margin-top:1rem; padding:1rem;
                 background:rgba(239,68,68,.08); border:1px solid rgba(239,68,68,.2);
                 border-radius:8px; font-family:var(--font-mono,'DM Mono',monospace);
                 font-size:.8rem; color:#dc2626;"></div>
        </div>
    </div>

</div>
</main>

<!-- ── FOOTER ── -->
<footer class="footer">
    <div class="footer-inner">
        <p class="footer-logo">AddictData</p>
        <p class="footer-copy">
            Projet universitaire — Science des données 4 &nbsp;·&nbsp; 2025–2026 &nbsp;·&nbsp;
            L3 MIASHS Université Paul Valéry Montpellier &nbsp;·&nbsp;
            Données à usage strictement pédagogique.
        </p>
    </div>
</footer>

<script>
// ── Helpers ──────────────────────────────────────────
function setVal(id, val) { document.getElementById(id).textContent = val; }

// ── Chart defaults (mêmes que social_addiction.php) ──
const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { family: 'DM Mono', size: 11 }, color: '#888' } },
        y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { family: 'DM Mono', size: 11 }, color: '#888' } }
    }
};

const levelColors = {
    low:      'rgba(22,163,74,.75)',
    moderate: 'rgba(245,158,11,.8)',
    high:     'rgba(239,68,68,.75)',
    severe:   'rgba(124,58,237,.75)'
};

// 1 — Distribution des niveaux d'addiction (vraies données)
new Chart(document.getElementById('chartDistribution'), {
    type: 'doughnut',
    data: {
        labels: ['High (25.5%)', 'Low (25.2%)', 'Severe (25.0%)', 'Moderate (24.3%)'],
        datasets: [{
            data: [25.5, 25.2, 25.0, 24.3],
            backgroundColor: [levelColors.high, levelColors.low, levelColors.severe, levelColors.moderate],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '62%',
        plugins: {
            legend: { display: true, position: 'right',
                labels: { font: { family: 'DM Mono', size: 11 }, color: '#888', padding: 14 } }
        }
    }
});

// 2 — Temps d'écran par niveau (vraies données)
new Chart(document.getElementById('chartScreenTime'), {
    type: 'bar',
    data: {
        labels: ['Low', 'Moderate', 'High', 'Severe'],
        datasets: [{
            label: 'Heures / jour',
            data: [6.1, 5.9, 6.0, 6.0],
            backgroundColor: [levelColors.low, levelColors.moderate, levelColors.high, levelColors.severe],
            borderRadius: 6, borderSkipped: false
        }]
    },
    options: {
        ...chartDefaults,
        scales: {
            y: { ...chartDefaults.scales.y, beginAtZero: false, min: 5.5, max: 6.5,
                 title: { display: true, text: 'Heures / jour', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } },
            x: { ...chartDefaults.scales.x }
        }
    }
});

// 3 — Radar santé mentale (vraies données)
new Chart(document.getElementById('chartMental'), {
    type: 'radar',
    data: {
        labels: ['Dépression', 'Anxiété', 'Stress', 'Bien-être'],
        datasets: [
            { label: 'Low',    data: [50.5, 48.4, 49.5, 50.3], backgroundColor: 'rgba(22,163,74,.15)',  borderColor: levelColors.low,    pointBackgroundColor: levelColors.low },
            { label: 'Severe', data: [50.6, 50.0, 51.1, 48.2], backgroundColor: 'rgba(124,58,237,.12)', borderColor: levelColors.severe, pointBackgroundColor: levelColors.severe }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        scales: { r: { beginAtZero: false, min: 40, max: 60,
            ticks: { font: { family: 'DM Mono', size: 9 }, color: '#aaa' },
            pointLabels: { font: { family: 'DM Mono', size: 10 }, color: '#888' }
        }},
        plugins: { legend: { display: true, position: 'bottom',
            labels: { font: { family: 'DM Mono', size: 11 }, color: '#888', padding: 14 }
        }}
    }
});

// 4 — Âge premier téléphone (vraies données)
new Chart(document.getElementById('chartFirstPhone'), {
    type: 'bar',
    data: {
        labels: ['< 10 ans', '10–13 ans', '13–16 ans', '16–18 ans', '18+ ans'],
        datasets: [{
            label: '% High/Severe',
            data: [51.9, 50.5, 49.5, 48.4, 51.8],
            backgroundColor: [
                levelColors.high, levelColors.moderate,
                'rgba(251,146,60,.6)', 'rgba(22,163,74,.55)', levelColors.low
            ],
            borderRadius: 6, borderSkipped: false
        }]
    },
    options: {
        ...chartDefaults,
        indexAxis: 'y',
        scales: {
            x: { ...chartDefaults.scales.x, min: 40, max: 60,
                 ticks: { ...chartDefaults.scales.x.ticks, callback: v => v + '%' } },
            y: { ...chartDefaults.scales.y, grid: { display: false } }
        }
    }
});

// 5 — App de gestion (vraies données)
new Chart(document.getElementById('chartApp'), {
    type: 'bar',
    data: {
        labels: ['Low', 'Moderate', 'High', 'Severe'],
        datasets: [
            { label: 'Sans app', data: [24.3, 26.0, 23.8, 25.9], backgroundColor: 'rgba(239,68,68,.6)',  borderRadius: 4 },
            { label: 'Avec app', data: [26.2, 22.6, 27.2, 24.0], backgroundColor: 'rgba(22,163,74,.65)', borderRadius: 4 }
        ]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: true, position: 'top',
                labels: { font: { family: 'DM Mono', size: 11 }, color: '#888', padding: 16 } }
        },
        scales: {
            y: { ...chartDefaults.scales.y, beginAtZero: true, max: 35,
                 ticks: { ...chartDefaults.scales.y.ticks, callback: v => v + '%' } },
            x: { ...chartDefaults.scales.x }
        }
    }
});

// ── Graphique de positionnement ───────────────────────────────────────────────
let resultPositionChart = null;
const distLabels = ['Low', 'Moderate', 'High', 'Severe'];
const distData   = [25.2, 24.3, 25.5, 25.0];

function updateResultPositionChart(level) {
    const levelIdx = { 'Low':0,'Moderate':1,'High':2,'Severe':3 };
    const idx = levelIdx[level] ?? 0;
    const colors = distLabels.map((_, i) =>
        i === idx ? Object.values(levelColors)[i] : 'rgba(45,47,61,.3)'
    );

    if (resultPositionChart) {
        resultPositionChart.data.datasets[0].backgroundColor = colors;
        resultPositionChart.update();
    } else {
        resultPositionChart = new Chart(document.getElementById('chartResultPosition'), {
            type: 'bar',
            data: {
                labels: distLabels,
                datasets: [{ data: distData, backgroundColor: colors, borderRadius: 5 }]
            },
            options: {
                ...chartDefaults,
                plugins: { legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y}% de l'échantillon` } }
                },
                scales: {
                    x: { ...chartDefaults.scales.x },
                    y: { ...chartDefaults.scales.y,
                         title: { display: true, text: '% de l\'échantillon',
                                  font: { size: 9, family: 'DM Mono' }, color: '#aaa' } }
                }
            }
        });
    }
}

// ── Scroll reveal ──────────────────────────────────────────────────────────────
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }
    });
}, { threshold: 0.08 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// ── Prédiction ────────────────────────────────────────────────────────────────
document.getElementById('predictionForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = this.querySelector('.btn-predict');
    btn.textContent = 'Calcul en cours…';
    btn.disabled = true;

    document.getElementById('predictionError').style.display  = 'none';
    document.getElementById('predictionResult').classList.remove('visible');

    const formData = new FormData(this);
    formData.append('action', 'predict');

    try {
        const response = await fetch('mobile_addiction.php', { method: 'POST', body: formData });
        const data = await response.json();

        if (data.error) {
            document.getElementById('predictionError').textContent = 'Erreur : ' + data.error + (data.raw ? ' | ' + data.raw : '');
            document.getElementById('predictionError').style.display = 'block';
        } else {
            const score    = parseFloat(data.score).toFixed(1);
            const level    = data.level || 'Low';
            const fiab     = data.fiabilite !== undefined ? data.fiabilite : 78;
            const factors  = data.factors  || [];
            const pct      = Math.round((parseFloat(score) / 10) * 100);

            // Classe CSS du niveau
            const levelClass = level.toLowerCase();

            document.getElementById('resultScore').textContent     = score;
            document.getElementById('resultLevel').textContent     = level;
            document.getElementById('resultLevel').className       = 'result-level ' + levelClass;
            document.getElementById('resultConfidence').textContent = `Fiabilité du modèle : ${fiab}% de confiance`;

            // Afficher
            const result = document.getElementById('predictionResult');
            result.classList.add('visible');

            // Barre de progression
            setTimeout(() => { document.getElementById('resultBar').style.width = pct + '%'; }, 50);

            // Facteurs
            const factorsEl = document.getElementById('resultFactors');
            const factSection = document.getElementById('resultFactorsSection');
            if (factors.length) {
                factorsEl.innerHTML = factors.map(f => `<span class="factor-tag">${f}</span>`).join('');
                factSection.style.display = 'block';
            } else {
                factSection.style.display = 'none';
            }

            // Graphique de positionnement
            setTimeout(() => updateResultPositionChart(level), 100);

            // Conseil si High ou Severe
            document.getElementById('proAdvice').style.display =
                (level === 'High' || level === 'Severe') ? 'block' : 'none';
        }
    } catch (err) {
        document.getElementById('predictionError').textContent = 'Impossible de joindre le serveur.';
        document.getElementById('predictionError').style.display = 'block';
    }

    btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg> Calculer mon niveau`;
    btn.disabled = false;
});
</script>
</body>
</html>