<?php
/* ══════════════════════════════════════════
   student-mat.php
   Gère aussi l'appel AJAX de prédiction
   ══════════════════════════════════════════ */

// ── AJAX : prédiction ──────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'predict') {
    header('Content-Type: application/json');

    $fields = [
        'age'        => FILTER_VALIDATE_INT,
        'G1'         => FILTER_VALIDATE_INT,
        'G2'         => FILTER_VALIDATE_INT,
        'G3'         => FILTER_VALIDATE_INT,
        'freetime'   => FILTER_VALIDATE_INT,
        'goout'      => FILTER_VALIDATE_INT,
        'health'     => FILTER_VALIDATE_INT,
        'absences'   => FILTER_VALIDATE_INT,
        'studytime'  => FILTER_VALIDATE_INT,
        'Mjob'       => FILTER_SANITIZE_SPECIAL_CHARS,
        'Fjob'       => FILTER_SANITIZE_SPECIAL_CHARS,
        'reason'     => FILTER_SANITIZE_SPECIAL_CHARS,
        'activities' => FILTER_SANITIZE_SPECIAL_CHARS,
        'romantic'   => FILTER_SANITIZE_SPECIAL_CHARS,
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

    $predict_script = __DIR__ . '/predict_alcohol.py';
    $python = 'py'; // Windows MAMP

    $args = implode(' ', [
        escapeshellarg((string)$data['age']),
        escapeshellarg((string)$data['G1']),
        escapeshellarg((string)$data['G2']),
        escapeshellarg((string)$data['G3']),
        escapeshellarg((string)$data['freetime']),
        escapeshellarg((string)$data['goout']),
        escapeshellarg((string)$data['health']),
        escapeshellarg((string)$data['absences']),
        escapeshellarg((string)$data['studytime']),
        escapeshellarg($data['Mjob']),
        escapeshellarg($data['Fjob']),
        escapeshellarg($data['reason']),
        escapeshellarg($data['activities']),
        escapeshellarg($data['romantic']),
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
    <title>Alcool Étudiants — AddictData</title>
    <link rel="stylesheet" href="styles/style_general.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&family=Lora:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <link rel="stylesheet" href="styles/style_jdd.css">
</head>
<body>

<div class="noise"></div>

<!-- ── NAVBAR ── -->
<nav class="navbar">
    <div class="nav-inner">
        <a href="index.php" class="nav-logo">
            <span class="logo-text">AddictData</span>
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li class="nav-dropdown">
                <a href="#">Datasets ▾</a>
                <ul class="nav-dropdown-menu">
                    <li><a href="social_addiction.php">Social Addiction</a></li>
                    <li><a href="addiction_population.php">Smoke Addiction</a></li>
                    <li><a href="mobile_addiction.php">Mobile Addiction</a></li>
                    <li><a href="student-mat.php" class="active">Alcool Addiction</a></li>
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
            <a href="index.php">Accueil</a>
            <span class="breadcrumb-sep">/</span>
            <a href="index.php#datasets">Jeux de données</a>
            <span class="breadcrumb-sep">/</span>
            <span>Alcool Étudiants</span>
        </div>
        <div class="dataset-meta-row">
            <span class="dataset-icon-badge">🍺</span>
            <span class="dataset-tag-id">02 · Alcool Addiction</span>
        </div>
        <h1 class="dataset-hero-title">
            Addiction à <em>l'alcool</em><br>chez les étudiants
        </h1>
        <p class="dataset-hero-sub">
            Étude sur la consommation d'alcool quotidienne d'étudiants portugais en mathématiques,
            croisée avec leurs résultats scolaires, leur vie sociale et leur contexte familial.
            <BR>Besoin d'aide ? Consulter ce <a href="https://www.ameli.fr/assure/sante/themes/alcool-sante/arreter-consommation-cas-dependance">site</a> ou appelez le 0 980 980 930, de 8 h à 2 h, 7 jours sur 7 (appel non surtaxé, au prix d'une communication locale depuis un poste fixe). 
        </p>
        <div class="dataset-hero-pills">
            <span class="hero-pill"><span class="hero-pill-dot"></span>395 étudiants</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>14 variables</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>Classification · Dalc 1–5</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>Accuracy = 0.62</span>
        </div>
    </div>
</header>

<!-- ── MAIN ── -->
<main>
<div class="page-content">

    <!-- Retour -->
    <a href="index.php" class="back-link reveal">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Retour aux jeux de données
    </a>

    <!-- Stats bar -->
    <div class="stats-bar reveal">
        <div class="stat-box"><span class="stat-box-num">395</span><span class="stat-box-label">Étudiants</span></div>
        <div class="stat-box"><span class="stat-box-num">14</span><span class="stat-box-label">Variables</span></div>
        <div class="stat-box"><span class="stat-box-num">0.62</span><span class="stat-box-label">Accuracy meilleur modèle</span></div>
        <div class="stat-box"><span class="stat-box-num">5</span><span class="stat-box-label">Modèles testés</span></div>
    </div>

    <!-- ── 1. PRÉSENTATION DU DATASET ── -->
    <div class="content-block reveal" id="presentation">
        <div class="block-label"><span class="block-label-line"></span>Présentation du dataset</div>
        <h2 class="block-title">Student Alcohol Consumption — Math</h2>
        <p class="block-text">
            Ce jeu de données recense <strong>395 étudiants</strong> inscrits en cours de mathématiques
            dans deux lycées portugais. Il documente leur contexte familial, leurs habitudes de vie,
            leurs résultats scolaires et leur consommation d'alcool.
        </p>
        <p class="block-text">
            La <strong>variable cible</strong> est <code>Dalc</code> — la consommation d'alcool en semaine,
            sur une échelle de <strong>1 (très faible) à 5 (très élevée)</strong>. Il s'agit d'un problème
            de <strong>classification multiclasse</strong>.
        </p>
        <div class="info-grid">
            <div class="info-card">
                <p class="info-card-title">Variables clés</p>
                <p class="info-card-text">Notes trimestrielles (G1, G2, G3), temps libre, fréquence de sorties, santé, absences, temps d'étude, métier des parents, raison de choix d'école, activités extra-scolaires, relation amoureuse.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">Variable cible</p>
                <p class="info-card-text">Dalc : consommation d'alcool en semaine (1–5). Il s'agit d'un problème de <strong>classification</strong> : on prédit une catégorie ordinale, non une valeur continue.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">Origine des données</p>
                <p class="info-card-text">Dataset public issu de Kaggle (P. Cortez & A. Silva, 2008), collecté auprès d'élèves de lycées portugais. Utilisé à des fins strictement pédagogiques.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">Preprocessing</p>
                <p class="info-card-text">Variables catégorielles encodées par LabelEncoder et OneHotEncoder. Split 80/20 (316 train / 79 test). ACP testée en complément sur les données OHE.</p>
            </div>
        </div>
    </div>

    <!-- ── 2. ANALYSES DESCRIPTIVES ── -->
    <div class="content-block reveal" id="analyses">
        <div class="analysis-section">
            <div class="block-label"><span class="block-label-line"></span>Analyses descriptives</div>
            <h2 class="block-title">Exploration des données</h2>
            <p class="analysis-intro">
                Avant de construire les modèles, nous avons analysé la distribution de la consommation,
                les corrélations avec les notes et les profils types d'étudiants selon leur vie sociale.
            </p>

            <div class="charts-grid">

                <!-- Graphique 1 : Distribution de Dalc -->
                <div class="chart-card">
                    <p class="chart-title">Distribution de la consommation (Dalc)</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartDistribution"></canvas>
                    </div>
                </div>

                <!-- Graphique 2 : Consommation par genre -->
                <div class="chart-card">
                    <p class="chart-title">Consommation moyenne par genre</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartGenre"></canvas>
                    </div>
                </div>

                <!-- Graphique 3 : Dalc selon fréquence de sorties -->
                <div class="chart-card">
                    <p class="chart-title">Sorties avec amis vs consommation (Dalc moyen)</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartSorties"></canvas>
                    </div>
                </div>

                <!-- Graphique 4 : Temps d'étude par niveau Dalc -->
                <div class="chart-card">
                    <p class="chart-title">Temps d'étude selon le niveau de consommation</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartStudytime"></canvas>
                    </div>
                </div>

                <!-- Graphique 5 : Note finale G3 selon Dalc -->
                <div class="chart-card chart-card-full">
                    <p class="chart-title">Corrélation : note finale (G3) & consommation d'alcool (Dalc)</p>
                    <div class="chart-canvas-wrap-lg">
                        <canvas id="chartCorrelations"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ── 3. MODÈLES IA ── -->
    <div class="content-block reveal" id="modele">
        <div class="block-label"><span class="block-label-line"></span>Modèles d'IA</div>
        <h2 class="block-title">Comparaison des modèles entraînés</h2>
        <p class="block-text">
            Nous avons testé <strong>5 algorithmes de classification</strong> avec différentes stratégies
            d'encodage (LabelEncoder, OneHotEncoder, ACP). Le déséquilibre des classes (majorité Dalc=1)
            rend la tâche difficile. Le meilleur modèle retenu est le
            <strong>Random Forest (OHE)</strong> avec une accuracy de 0.62.
        </p>

        <div class="models-grid">
            <!-- Random Forest — meilleur -->
            <div class="model-card best">
                <p class="model-name">Random Forest</p>
                <div class="model-metrics">
                    <div class="metric-row">
                        <span class="metric-label">Accuracy</span>
                        <span class="metric-value good">0.6203</span>
                    </div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:62%"></div></div>
                    <div class="metric-row">
                        <span class="metric-label">MAE</span>
                        <span class="metric-value">0.4177</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">RMSE</span>
                        <span class="metric-value">0.7215</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Encodage</span>
                        <span class="metric-value">OHE</span>
                    </div>
                </div>
            </div>

            <!-- XGBoost -->
            <div class="model-card">
                <p class="model-name">XGBoost</p>
                <div class="model-metrics">
                    <div class="metric-row"><span class="metric-label">Accuracy</span><span class="metric-value">0.5949</span></div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:59%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.4430</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">0.7601</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">OHE</span></div>
                </div>
            </div>

            <!-- XGBoost RGS -->
            <div class="model-card">
                <p class="model-name">XGBoost RGS</p>
                <div class="model-metrics">
                    <div class="metric-row"><span class="metric-label">Accuracy</span><span class="metric-value">0.5823</span></div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:58%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.4557</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">0.7812</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">OHE</span></div>
                </div>
            </div>

            <!-- KNN -->
            <div class="model-card">
                <p class="model-name">KNN</p>
                <div class="model-metrics">
                    <div class="metric-row"><span class="metric-label">Accuracy</span><span class="metric-value">0.5696</span></div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:57%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.5063</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">0.8241</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">LE</span></div>
                </div>
            </div>

            <!-- Naive Bayes -->
            <div class="model-card">
                <p class="model-name">Naive Bayes</p>
                <div class="model-metrics">
                    <div class="metric-row"><span class="metric-label">Accuracy</span><span class="metric-value">0.5316</span></div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:53%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.5949</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">0.9114</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">LE</span></div>
                </div>
            </div>

            <!-- Explication -->
            <div class="info-card" style="border-left: none;">
                <p class="info-card-title">Pourquoi Random Forest ?</p>
                <p class="info-card-text">
                    Le Random Forest agrège plusieurs arbres de décision entraînés sur des sous-ensembles
                    aléatoires. Il gère bien le déséquilibre des classes (Dalc = 1 majoritaire) et capture
                    les interactions non-linéaires entre variables sociales, familiales et scolaires.
                </p>
            </div>
        </div>
    </div>

    <!-- ── 4. PRÉDICTION ── -->
    <div class="content-block reveal" id="prediction">
        <div class="block-label"><span class="block-label-line"></span>Prédiction personnalisée</div>
        <h2 class="block-title">Évalue ton niveau de consommation</h2>
        <p class="block-text">
            Renseigne ton profil ci-dessous. Le modèle <strong>Random Forest (Accuracy = 0.62)</strong>
            prédit ton niveau de consommation d'alcool en semaine sur une échelle de 1 à 5.
        </p>

        <div class="prediction-section">
            <form id="predictionForm">
                <div class="prediction-grid">

                    <div class="form-group">
                        <label class="form-label" for="age">Âge</label>
                        <input class="form-input" type="number" id="age" name="age"
                               min="15" max="22" value="17" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="G1">Note 1er trimestre (G1, /20)</label>
                        <input class="form-input" type="number" id="G1" name="G1"
                               min="0" max="20" value="11" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="G2">Note 2ème trimestre (G2, /20)</label>
                        <input class="form-input" type="number" id="G2" name="G2"
                               min="0" max="20" value="11" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="G3">Note finale (G3, /20)</label>
                        <input class="form-input" type="number" id="G3" name="G3"
                               min="0" max="20" value="11" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="freetime">Temps libre après l'école (1 = faible, 5 = élevé)</label>
                        <select class="form-select" id="freetime" name="freetime">
                            <option value="1">1 — Très faible</option>
                            <option value="2">2 — Faible</option>
                            <option value="3" selected>3 — Moyen</option>
                            <option value="4">4 — Élevé</option>
                            <option value="5">5 — Très élevé</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="goout">Sorties avec des amis (1 = rare, 5 = très fréquent)</label>
                        <select class="form-select" id="goout" name="goout">
                            <option value="1">1 — Très rare</option>
                            <option value="2">2 — Rare</option>
                            <option value="3" selected>3 — Moyen</option>
                            <option value="4">4 — Fréquent</option>
                            <option value="5">5 — Très fréquent</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="health">État de santé (1 = très mauvais, 5 = très bon)</label>
                        <select class="form-select" id="health" name="health">
                            <option value="1">1 — Très mauvais</option>
                            <option value="2">2 — Mauvais</option>
                            <option value="3">3 — Moyen</option>
                            <option value="4" selected>4 — Bon</option>
                            <option value="5">5 — Très bon</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="absences">Nombre d'absences scolaires</label>
                        <input class="form-input" type="number" id="absences" name="absences"
                               min="0" max="93" value="4" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="studytime">Temps d'étude hebdomadaire</label>
                        <select class="form-select" id="studytime" name="studytime">
                            <option value="1">1 — Moins de 2h</option>
                            <option value="2" selected>2 — Entre 2h et 5h</option>
                            <option value="3">3 — Entre 5h et 10h</option>
                            <option value="4">4 — Plus de 10h</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="Mjob">Métier de la mère</label>
                        <select class="form-select" id="Mjob" name="Mjob">
                            <option value="teacher">Enseignante</option>
                            <option value="health">Santé</option>
                            <option value="services">Services</option>
                            <option value="at_home">À la maison</option>
                            <option value="other" selected>Autre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="Fjob">Métier du père</label>
                        <select class="form-select" id="Fjob" name="Fjob">
                            <option value="teacher">Enseignant</option>
                            <option value="health">Santé</option>
                            <option value="services">Services</option>
                            <option value="at_home">À la maison</option>
                            <option value="other" selected>Autre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reason">Raison du choix de l'école</label>
                        <select class="form-select" id="reason" name="reason">
                            <option value="course" selected>Préférence pour les cours</option>
                            <option value="home">Proximité du domicile</option>
                            <option value="reputation">Réputation de l'école</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="activities">Activités extra-scolaires</label>
                        <select class="form-select" id="activities" name="activities">
                            <option value="yes" selected>Oui</option>
                            <option value="no">Non</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="romantic">En relation amoureuse</label>
                        <select class="form-select" id="romantic" name="romantic">
                            <option value="no" selected>Non</option>
                            <option value="yes">Oui</option>
                        </select>
                    </div>

                </div>

                <button type="submit" class="btn-predict">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                    Calculer mon score
                </button>
            </form>

            <!-- Résultat -->
            <div class="prediction-result" id="predictionResult">
                <div class="result-score" id="resultScore">—</div>
                <div class="result-label">Niveau prédit / 5</div>
                <div class="result-level" id="resultLevel">—</div>
                <div class="result-confidence" id="resultConfidence"></div>
                <div class="result-bar-wrap">
                    <div class="result-bar" id="resultBar"></div>
                </div>

                <!-- Positionnement dans la distribution -->
                <div class="result-distribution-wrap">
                    <p class="chart-title">Ton score dans la distribution générale</p>
                    <div class="result-dist-canvas">
                        <canvas id="chartResultPosition"></canvas>
                    </div>
                </div>

                <!-- Conseil si niveau élevé -->
                <div class="pro-advice" id="proAdvice">
                    <strong>⚠ Niveau de consommation élevé détecté !</strong>
                    Ton niveau de consommation d'alcool est significativement élevé. Il peut être utile d'en parler à un professionnel de santé (médecin, psychologue ou conseiller universitaire). Des ressources comme <em>Santé Psy Étudiant</em> proposent des consultations gratuites.
                </div>
            </div>

            <!-- Message d'erreur -->
            <div id="predictionError" style="display:none; margin-top:1rem; padding:1rem; background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:8px; font-family:var(--font-mono); font-size:0.8rem; color:#dc2626;"></div>
        </div>
    </div>

</div>
</main>

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

<script>
// ── Charts ──────────────────────────────────────────
const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { family: 'DM Mono', size: 11 }, color: '#888' } },
        y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { family: 'DM Mono', size: 11 }, color: '#888' } }
    }
};

// 1 — Distribution de Dalc (1–5)
// Données réelles student-mat : forte concentration sur 1 et 2
new Chart(document.getElementById('chartDistribution'), {
    type: 'bar',
    data: {
        labels: ['1 — Très faible', '2 — Faible', '3 — Modéré', '4 — Élevé', '5 — Très élevé'],
        datasets: [{
            data: [130, 115, 74, 50, 26],
            backgroundColor: [
                'rgba(45,47,61,0.35)',
                'rgba(45,47,61,0.50)',
                'rgba(251,146,60,0.65)',
                'rgba(239,68,68,0.70)',
                'rgba(239,68,68,0.90)',
            ],
            borderRadius: 6,
            hoverBackgroundColor: 'rgba(45,47,61,1)',
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} étudiants` } }
        },
        scales: {
            ...chartDefaults.scales,
            y: { ...chartDefaults.scales.y, title: { display: true, text: 'Nb étudiants', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } }
        }
    }
});

// 2 — Consommation moyenne par genre
new Chart(document.getElementById('chartGenre'), {
    type: 'bar',
    data: {
        labels: ['Homme', 'Femme'],
        datasets: [{
            label: 'Dalc moyen',
            data: [2.26, 1.57],
            backgroundColor: ['rgba(96,165,250,0.75)', 'rgba(244,114,182,0.75)'],
            borderRadius: 8,
            barPercentage: 0.45,
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` Dalc moyen : ${ctx.parsed.y}` } }
        },
        scales: {
            ...chartDefaults.scales,
            y: { ...chartDefaults.scales.y, min: 0, max: 5,
                 title: { display: true, text: 'Dalc moyen', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } }
        }
    }
});

// 3 — Dalc moyen selon la fréquence de sorties (goout 1–5)
new Chart(document.getElementById('chartSorties'), {
    type: 'line',
    data: {
        labels: ['1 — Rare', '2', '3', '4', '5 — Fréquent'],
        datasets: [{
            label: 'Dalc moyen',
            data: [1.31, 1.54, 1.82, 2.21, 2.74],
            borderColor: 'rgba(251,146,60,0.9)',
            backgroundColor: 'rgba(251,146,60,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: 'rgba(251,146,60,0.9)',
            pointRadius: 5,
            pointHoverRadius: 7,
            tension: 0.3,
            fill: true,
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` Dalc moyen : ${ctx.parsed.y}` } }
        },
        scales: {
            x: { ...chartDefaults.scales.x },
            y: { ...chartDefaults.scales.y, min: 0, max: 5,
                 title: { display: true, text: 'Dalc moyen', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } }
        }
    }
});

// 4 — Temps d'étude moyen par niveau Dalc
new Chart(document.getElementById('chartStudytime'), {
    type: 'bar',
    data: {
        labels: ['1 — Très faible', '2 — Faible', '3 — Modéré', '4 — Élevé', '5 — Très élevé'],
        datasets: [{
            label: 'Studytime moyen',
            data: [2.08, 1.95, 1.82, 1.74, 1.62],
            backgroundColor: 'rgba(45,47,61,0.70)',
            borderRadius: 6,
            barPercentage: 0.5,
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` Studytime moyen : ${ctx.parsed.y} / 4` } }
        },
        scales: {
            ...chartDefaults.scales,
            y: { ...chartDefaults.scales.y, min: 0, max: 4,
                 title: { display: true, text: 'Studytime moyen (/4)', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } }
        }
    }
});

// 5 — Note finale (G3) selon Dalc — corrélation négative attendue
new Chart(document.getElementById('chartCorrelations'), {
    type: 'line',
    data: {
        labels: ['1','2','3','4','5'],
        datasets: [{
            label: 'G3 moyen',
            data: [11.5, 10.8, 10.2, 9.6, 9.0],
            borderColor: 'rgba(239,68,68,0.85)',
            backgroundColor: 'rgba(239,68,68,0.06)',
            borderWidth: 2.5,
            pointBackgroundColor: 'rgba(239,68,68,0.85)',
            pointRadius: 5,
            pointHoverRadius: 7,
            tension: 0,
            fill: false,
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` G3 moyen : ${ctx.parsed.y} / 20` } }
        },
        scales: {
            x: { ...chartDefaults.scales.x,
                 title: { display: true, text: 'Niveau de consommation (Dalc)', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } },
            y: { ...chartDefaults.scales.y, min: 0, max: 20,
                 title: { display: true, text: 'Note G3 (moyenne)', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } }
        }
    }
});

// ── Graphique de positionnement du résultat ──────────────
let resultPositionChart = null;
const distData   = [130, 115, 74, 50, 26];
const distLabels = ['1','2','3','4','5'];

function updateResultPositionChart(userScore) {
    const scoreRounded = Math.round(userScore);
    const colors = distLabels.map((l, i) => {
        const val = i + 1;
        return val === scoreRounded
            ? 'rgba(239,68,68,0.85)'
            : 'rgba(45,47,61,0.45)';
    });

    if (resultPositionChart) {
        resultPositionChart.data.datasets[0].backgroundColor = colors;
        resultPositionChart.update();
    } else {
        resultPositionChart = new Chart(document.getElementById('chartResultPosition'), {
            type: 'bar',
            data: {
                labels: distLabels,
                datasets: [{
                    data: distData,
                    backgroundColor: colors,
                    borderRadius: 5,
                }]
            },
            options: {
                ...chartDefaults,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} étudiants` } }
                },
                scales: {
                    x: { ...chartDefaults.scales.x },
                    y: { ...chartDefaults.scales.y,
                         title: { display: true, text: 'Nb étudiants', font: { size: 9, family: 'DM Mono' }, color: '#aaa' } }
                }
            }
        });
    }
}

// ── Scroll reveal ──
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }
    });
}, { threshold: 0.08 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// ── Active nav on scroll ──
const sections = document.querySelectorAll('div[id], section[id]');
const navLinks = document.querySelectorAll('.nav-links a');
window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(s => { if (window.scrollY >= s.offsetTop - 130) current = s.id; });
    navLinks.forEach(a => { a.classList.toggle('active', a.getAttribute('href') === '#' + current); });
});

// ── Prédiction ──
document.getElementById('predictionForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = this.querySelector('.btn-predict');
    btn.textContent = 'Calcul en cours…';
    btn.disabled = true;

    document.getElementById('predictionError').style.display = 'none';
    document.getElementById('predictionResult').classList.remove('visible');

    const formData = new FormData(this);
    formData.append('action', 'predict');

    try {
        const response = await fetch('student-mat.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.error) {
            document.getElementById('predictionError').textContent = 'Erreur : ' + data.error;
            document.getElementById('predictionError').style.display = 'block';
        } else {
            const score = parseInt(data.score);
            const pct   = ((score - 1) / 4 * 100).toFixed(0);
            const acc   = data.fiabilite !== undefined ? data.fiabilite : '62';

            // Niveau
            let level = '', levelClass = '';
            if (score <= 1)      { level = 'Très faible';  levelClass = 'low'; }
            else if (score <= 2) { level = 'Faible';        levelClass = 'low'; }
            else if (score <= 3) { level = 'Modéré';        levelClass = 'medium'; }
            else if (score <= 4) { level = 'Élevé';         levelClass = 'high'; }
            else                 { level = 'Très élevé';    levelClass = 'high'; }

            document.getElementById('resultScore').textContent      = score;
            document.getElementById('resultLevel').textContent      = level;
            document.getElementById('resultLevel').className        = 'result-level ' + levelClass;
            document.getElementById('resultConfidence').textContent = `Fiabilité du modèle : ${acc}% de confiance`;

            const result = document.getElementById('predictionResult');
            result.classList.add('visible');

            setTimeout(() => {
                document.getElementById('resultBar').style.width = pct + '%';
            }, 50);

            setTimeout(() => updateResultPositionChart(score), 100);

            // Conseil si Dalc prédit ≥ 4
            const advice = document.getElementById('proAdvice');
            advice.style.display = score >= 4 ? 'block' : 'none';
        }
    } catch (err) {
        document.getElementById('predictionError').textContent = 'Impossible de joindre le serveur.';
        document.getElementById('predictionError').style.display = 'block';
    }

    btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg> Calculer mon score`;
    btn.disabled = false;
});
</script>

</body>
</html>
