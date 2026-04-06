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

    // ── Appel à l'API Render ──
    $api_url = 'https://addiction-api.onrender.com/predict/mobile';

    $payload = json_encode([
    'age'         => (int)$data['age'],
    'gender'      => $data['gender'],
    'occupation'  => $data['occupation'],
    'education'   => $data['education'],       // ← non Education_Level
    'screen_time' => (float)$data['screen_time'],
    'unlocks'     => (int)$data['unlocks'],
    'social_hours'=> (float)$data['social_hours'],
    'sleep_hours' => (float)$data['sleep_hours'],
    'mental_health'=> (int)$data['mental_health'],
    'stress'      => (int)$data['stress'],
    'first_phone' => (int)$data['first_phone'],
    'has_app'     => $data['has_app'],
    'physical'    => (float)$data['physical'],
    ]);

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER,     ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT,        30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $curl_err = curl_error($ch);
    curl_close($ch);

    if ($curl_err) {
        echo json_encode(['error' => 'Impossible de joindre l\'API : ' . $curl_err]);
        exit;
    }

    $result = json_decode($response, true);
    if (!$result) {
        echo json_encode(['error' => 'Réponse invalide de l\'API', 'raw' => $response]);
    } elseif (isset($result['erreur'])) {
        echo json_encode(['error' => $result['erreur']]);
    } else {
        echo json_encode($result);
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
                    <li><a href="mobile_addiction.php" class="active">Mobile Addiction</a></li>
                    <li><a href="student-mat.php">Alcool Addiction</a></li>
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
            chez 5 000 individus à partir de données comportementales, socio-démographiques
            et de santé mentale.
        </p>
        <div class="dataset-hero-pills">
            <span class="hero-pill"><span class="hero-pill-dot"></span>5 000 observations</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>33 variables</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>Classification · 4 niveaux</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>MAE = 0.9634</span>
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
        <div class="stat-box"><span class="stat-box-num">5 000</span><span class="stat-box-label">Observations</span></div>
        <div class="stat-box"><span class="stat-box-num">33</span><span class="stat-box-label">Variables</span></div>
        <div class="stat-box"><span class="stat-box-num">0.96</span><span class="stat-box-label">MAE meilleur modèle</span></div>
        <div class="stat-box"><span class="stat-box-num">4</span><span class="stat-box-label">Modèles testés</span></div>
    </div>

    <!-- ── 1. PRÉSENTATION DU DATASET ── -->
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
                <p class="info-card-title">Variable cible</p>
                <p class="info-card-text">Addiction_Level (Low / Moderate / High / Severe). Problème de <strong>classification</strong> : on prédit l'une des 4 classes de dépendance numérique.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">Origine des données</p>
                <p class="info-card-text">Dataset public issu de Kaggle, compilé à partir d'enquêtes menées auprès d'étudiants et jeunes actifs. Utilisé à des fins strictement pédagogiques.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">Preprocessing</p>
                <p class="info-card-text">Suppression de User_ID, encodage LabelEncoder &amp; OneHotEncoder, normalisation StandardScaler. Split 80/20 stratifié (4 000 train / 1 000 test).</p>
            </div>
        </div>
    </div>

    <!-- ── 2. ANALYSES DESCRIPTIVES ── -->
    <div class="content-block reveal" id="analyses">
        <div class="analysis-section">
            <div class="block-label"><span class="block-label-line"></span>Analyses descriptives</div>
            <h2 class="block-title">Exploration des données</h2>
            <p class="analysis-intro">
                Exploration des variables clés : occupation, niveau d'éducation, impact d'une app
                de gestion du temps d'écran, âge au premier téléphone et comparaison par pays.
            </p>

            <div class="charts-grid">

                <div class="chart-card">
                    <p class="chart-title">Niveau d'addiction selon l'occupation</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartOccupation"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <p class="chart-title">Niveau d'addiction selon le niveau d'éducation</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartEducation"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <p class="chart-title">Impact de l'app de gestion du temps d'écran</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartApp"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <p class="chart-title">Addiction par tranche d'âge du 1er téléphone</p>
                    <div class="chart-canvas-wrap">
                        <canvas id="chartFirstPhone"></canvas>
                    </div>
                </div>

                <div class="chart-card chart-card-full">
                    <p class="chart-title">Comparaison par pays (Top 10) — Temps d'écran &amp; Score d'addiction</p>
                    <div class="chart-canvas-wrap-lg">
                        <canvas id="chartCountries"></canvas>
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
            Nous avons testé <strong>4 algorithmes</strong> avec deux stratégies
            d'encodage (LabelEncoder, OneHotEncoder). Le meilleur modèle retenu est le
            <strong>Random Forest (LE)</strong> avec un MAE de 0.9634.
        </p>

        <div class="models-grid">

            <div class="model-card best">
                <p class="model-name">Random Forest</p>
                <div class="model-metrics">
                    <div class="metric-row">
                        <span class="metric-label">R²</span>
                        <span class="metric-value good">-0.0047</span>
                    </div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:96%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.9634</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">1.0889</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">LE</span></div>
                </div>
            </div>

            <div class="model-card">
                <p class="model-name">XGBoost</p>
                <div class="model-metrics">
                    <div class="metric-row"><span class="metric-label">R²</span><span class="metric-value">-0.0081</span></div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:93%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.9646</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">1.0907</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">LE</span></div>
                </div>
            </div>

            <div class="model-card">
                <p class="model-name">KNN</p>
                <div class="model-metrics">
                    <div class="metric-row"><span class="metric-label">R²</span><span class="metric-value">-0.0601</span></div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:80%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.9726</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">1.1185</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">LE</span></div>
                </div>
            </div>

            <div class="model-card">
                <p class="model-name">Naive Bayes</p>
                <div class="model-metrics">
                    <div class="metric-row"><span class="metric-label">R²</span><span class="metric-value">-0.0151</span></div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:88%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.9623</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">1.0945</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">LE</span></div>
                </div>
            </div>

            <div class="info-card" style="border-left: none;">
                <p class="info-card-title">Pourquoi Random Forest ?</p>
                <p class="info-card-text">
                    Le Random Forest construit <strong>plusieurs arbres de décision</strong> sur des
                    sous-ensembles aléatoires des données, puis agrège leurs prédictions par vote.
                    Il est robuste au surapprentissage et capture bien les interactions non-linéaires
                    entre variables comportementales et psychologiques.
                </p>
            </div>

        </div>
    </div>

    <!-- ── 4. PRÉDICTION ── -->
    <div class="content-block reveal" id="prediction">
        <div class="block-label"><span class="block-label-line"></span>Prédiction personnalisée</div>
        <h2 class="block-title">Estime ton niveau d'addiction</h2>
        <p class="block-text">
            Renseigne ton profil ci-dessous. Le modèle <strong>Random Forest (LE)</strong>
            prédit ton niveau d'addiction mobile parmi : Low, Moderate, High, Severe.
        </p>

        <div class="prediction-section">
            <form id="predictionForm">
                <div class="prediction-grid">

                    <div class="form-group">
                        <label class="form-label" for="age">Âge</label>
                        <input class="form-input" type="number" id="age" name="age"
                               min="10" max="80" value="20" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="gender">Genre</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="Male">Homme</option>
                            <option value="Female" selected>Femme</option>
                            <option value="Other">Autre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="occupation">Occupation</label>
                        <select class="form-select" id="occupation" name="occupation">
                            <option value="Student" selected>Étudiant</option>
                            <option value="Employed">Salarié</option>
                            <option value="Unemployed">Sans emploi</option>
                            <option value="Freelancer">Freelance</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="education">Niveau d'éducation</label>
                        <select class="form-select" id="education" name="education">
                            <option value="High School" selected>Lycée</option>
                            <option value="Undergraduate">Licence</option>
                            <option value="Graduate">Master</option>
                            <option value="Postgraduate">Doctorat</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="screen_time">Temps d'écran / jour (h)</label>
                        <input class="form-input" type="number" id="screen_time" name="screen_time"
                               min="0" max="16" step="0.5" value="5" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="unlocks">Déverrouillages / jour</label>
                        <input class="form-input" type="number" id="unlocks" name="unlocks"
                               min="5" max="200" step="5" value="50" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="social_hours">Réseaux sociaux / jour (h)</label>
                        <input class="form-input" type="number" id="social_hours" name="social_hours"
                               min="0" max="12" step="0.5" value="3" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sleep_hours">Heures de sommeil / nuit</label>
                        <input class="form-input" type="number" id="sleep_hours" name="sleep_hours"
                               min="3" max="12" step="0.5" value="7" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="mental_health">Score santé mentale (0–20)</label>
                        <input class="form-input" type="number" id="mental_health" name="mental_health"
                               min="0" max="20" value="13" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="stress">Niveau de stress (0–30)</label>
                        <input class="form-input" type="number" id="stress" name="stress"
                               min="0" max="30" value="15" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="first_phone">Âge au 1er téléphone</label>
                        <input class="form-input" type="number" id="first_phone" name="first_phone"
                               min="5" max="25" value="13" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="has_app">App de gestion du temps</label>
                        <select class="form-select" id="has_app" name="has_app">
                            <option value="No" selected>Non</option>
                            <option value="Yes">Oui</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="physical">Activité physique / jour (h)</label>
                        <input class="form-input" type="number" id="physical" name="physical"
                               min="0" max="6" step="0.25" value="1" required>
                    </div>

                </div>

                <button type="submit" class="btn-predict">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                    Calculer mon niveau
                </button>
            </form>

            <!-- Résultat -->
            <div class="prediction-result" id="predictionResult">
                <div class="result-score" id="resultScore">—</div>
                <div class="result-label">Niveau prédit</div>
                <div class="result-level" id="resultLevel">—</div>
                <div class="result-confidence" id="resultConfidence"></div>
                <div class="result-bar-wrap">
                    <div class="result-bar" id="resultBar"></div>
                </div>

                <!-- Positionnement dans la distribution -->
                <div class="result-distribution-wrap">
                    <p class="chart-title">Ton niveau dans la distribution générale</p>
                    <div class="result-dist-canvas">
                        <canvas id="chartResultPosition"></canvas>
                    </div>
                </div>

                <!-- Conseil professionnel si niveau élevé -->
                <div class="pro-advice" id="proAdvice">
                    <strong>Niveau élevé détecté !</strong>
                    Ton niveau d'addiction est significativement élevé. Il peut être utile d'en parler
                    à un professionnel de santé mentale (médecin, psychologue ou conseiller universitaire).
                    Des ressources comme <em>Santé Psy Étudiant</em> proposent des consultations gratuites.
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

// 1 — Occupation (médiane du score 1-4)
new Chart(document.getElementById('chartOccupation'), {
    type: 'bar',
    data: {
        labels: ['Manager','Teacher','Artist','Doctor','Salesperson','Engineer','Student','Unemployed'],
        datasets: [{
            data: [3, 3, 3, 3, 2, 2, 2, 2],
            backgroundColor: [
                'rgba(102,194,165,0.8)','rgba(252,141,98,0.8)','rgba(141,160,203,0.8)',
                'rgba(231,138,195,0.8)','rgba(166,216,84,0.8)','rgba(255,217,47,0.8)',
                'rgba(229,196,148,0.8)','rgba(179,179,179,0.8)'
            ],
            borderRadius: 6,
        }]
    },
    options: {
        ...chartDefaults,
        indexAxis: 'y',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` Médiane : ${({1:'Low',2:'Moderate',3:'High',4:'Severe'}[ctx.parsed.x]||ctx.parsed.x)}` } }
        },
        scales: {
            x: { ...chartDefaults.scales.x, min: 0, max: 4,
                 ticks: { ...chartDefaults.scales.x.ticks, callback: v => ({1:'Low',2:'Moderate',3:'High',4:'Severe'}[v]||'') },
                 title: { display: true, text: 'Médiane (1=Low … 4=Severe)', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } },
            y: { ...chartDefaults.scales.y, grid: { display: false } }
        }
    }
});

// 2 — Education (médiane)
new Chart(document.getElementById('chartEducation'), {
    type: 'bar',
    data: {
        labels: ['PhD', 'High School', "Master's", "Bachelor's"],
        datasets: [{
            data: [3, 3, 3, 2],
            backgroundColor: [
                'rgba(63,0,125,0.75)','rgba(142,15,117,0.75)',
                'rgba(197,61,78,0.75)','rgba(234,162,112,0.75)'
            ],
            borderRadius: 6,
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` Médiane : ${({1:'Low',2:'Moderate',3:'High',4:'Severe'}[ctx.parsed.y]||ctx.parsed.y)}` } }
        },
        scales: {
            y: { ...chartDefaults.scales.y, min: 0, max: 4,
                 ticks: { ...chartDefaults.scales.y.ticks, callback: v => ({1:'Low',2:'Moderate',3:'High',4:'Severe'}[v]||'') },
                 title: { display: true, text: 'Médiane (1=Low … 4=Severe)', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } },
            x: { ...chartDefaults.scales.x }
        }
    }
});

// 3 — App gestion (médiane addiction + temps écran moyen)
new Chart(document.getElementById('chartApp'), {
    type: 'bar',
    data: {
        labels: ['Sans app (No)', 'Avec app (Yes)'],
        datasets: [
            { label: "Médiane addiction", data: [2, 3],
              backgroundColor: ['rgba(214,39,40,0.7)', 'rgba(31,119,180,0.7)'],
              borderRadius: 4, yAxisID: 'y' },
            { label: 'Temps écran moy. (h)', data: [5.95, 6.02],
              backgroundColor: ['rgba(214,39,40,0.25)', 'rgba(31,119,180,0.25)'],
              borderRadius: 4, yAxisID: 'y2' }
        ]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: true, position: 'top',
                labels: { font: { family: 'DM Mono', size: 11 }, color: '#888', padding: 14 } }
        },
        scales: {
            y:  { ...chartDefaults.scales.y, min: 0, max: 4, position: 'left',
                  ticks: { ...chartDefaults.scales.y.ticks, callback: v => ({1:'Low',2:'Moderate',3:'High',4:'Severe'}[v]||'') },
                  title: { display: true, text: 'Médiane addiction', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } },
            y2: { ...chartDefaults.scales.y, min: 5.8, max: 6.2, position: 'right', grid: { display: false },
                  title: { display: true, text: 'Heures / jour', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } },
            x:  { ...chartDefaults.scales.x }
        }
    }
});

// 4 — Âge premier téléphone (médiane)
new Chart(document.getElementById('chartFirstPhone'), {
    type: 'bar',
    data: {
        labels: ['< 10 ans', '10-13 ans', '13-16 ans', '16-18 ans', '18+ ans'],
        datasets: [{
            data: [3, 3, 2, 2, 3],
            backgroundColor: [
                'rgba(63,0,125,0.75)','rgba(107,0,128,0.75)',
                'rgba(168,45,100,0.75)','rgba(210,95,70,0.75)','rgba(234,162,112,0.75)'
            ],
            borderRadius: 6,
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` Médiane : ${({1:'Low',2:'Moderate',3:'High',4:'Severe'}[ctx.parsed.y]||ctx.parsed.y)}` } }
        },
        scales: {
            y: { ...chartDefaults.scales.y, min: 0, max: 4,
                 ticks: { ...chartDefaults.scales.y.ticks, callback: v => ({1:'Low',2:'Moderate',3:'High',4:'Severe'}[v]||'') },
                 title: { display: true, text: 'Médiane (1=Low … 4=Severe)', font: { size: 10, family: 'DM Mono' }, color: '#aaa' } },
            x: { ...chartDefaults.scales.x }
        }
    }
});

// 5 — Top 10 pays (grouped)
new Chart(document.getElementById('chartCountries'), {
    type: 'bar',
    data: {
        labels: ['India','Mexico','Russia','USA','UK','China','Germany','Brazil','Japan','Nigeria'],
        datasets: [
            { label: 'Temps écran (h/j)',
              data: [6.14, 6.13, 6.09, 6.06, 5.99, 5.91, 5.90, 5.89, 5.89, 5.86],
              backgroundColor: ['#1f77b4','#ff7f0e','#2ca02c','#d62728','#9467bd','#8c564b','#e377c2','#7f7f7f','#bcbd22','#17becf'],
              borderRadius: 4, yAxisID: 'y' },
            { label: 'Score addiction moy.',
              data: [2.43, 2.37, 2.43, 2.56, 2.58, 2.65, 2.51, 2.53, 2.49, 2.48],
              backgroundColor: ['#1f77b490','#ff7f0e90','#2ca02c90','#d6272890','#9467bd90','#8c564b90','#e377c290','#7f7f7f90','#bcbd2290','#17becf90'],
              borderRadius: 4, yAxisID: 'y2' }
        ]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: true, position: 'top',
                labels: { font: { family: 'DM Mono', size: 11 }, color: '#888', padding: 14 } }
        },
        scales: {
            y:  { position: 'left',  min: 5.7, max: 6.3, grid: { color: 'rgba(0,0,0,0.04)' },
                  title: { display: true, text: 'Heures / jour', font: { size: 10, family: 'DM Mono' }, color: '#aaa' },
                  ticks: { font: { family: 'DM Mono', size: 10 }, color: '#888' } },
            y2: { position: 'right', min: 0, max: 4, grid: { display: false },
                  title: { display: true, text: 'Score (1=Low, 4=Severe)', font: { size: 10, family: 'DM Mono' }, color: '#aaa' },
                  ticks: { font: { family: 'DM Mono', size: 10 }, color: '#888' } },
            x:  { ticks: { font: { family: 'DM Mono', size: 10 }, color: '#888', maxRotation: 35 }, grid: { display: false } }
        }
    }
});

// ── Graphique de positionnement du résultat ──────────────
let resultPositionChart = null;
const distData   = [25.2, 24.3, 25.5, 25.0];
const distLabels = ['Low', 'Moderate', 'High', 'Severe'];

function updateResultPositionChart(level) {
    const levelIdx = { 'Low': 0, 'Moderate': 1, 'High': 2, 'Severe': 3 };
    const idx = levelIdx[level] ?? 0;
    const colors = distLabels.map((_, i) =>
        i === idx ? 'rgba(239,68,68,0.85)' : 'rgba(45,47,61,0.55)'
    );

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
                    tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y}% de l'échantillon` } }
                },
                scales: {
                    x: { ...chartDefaults.scales.x },
                    y: { ...chartDefaults.scales.y, title: { display: true, text: '% échantillon', font: { size: 9, family: 'DM Mono' }, color: '#aaa' } }
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
        const response = await fetch('mobile_addiction.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.error) {
            document.getElementById('predictionError').textContent = 'Erreur : ' + data.error + (data.raw ? ' | ' + data.raw : '');
            document.getElementById('predictionError').style.display = 'block';
        } else {
            const level   = data.level || 'Low';
            const fiab    = data.fiabilite !== undefined ? data.fiabilite : 91;

            // Pourcentage barre : Low=25% Moderate=50% High=75% Severe=100%
            const levelPct = { 'Low': 25, 'Moderate': 50, 'High': 75, 'Severe': 100 };
            const pct      = levelPct[level] || 25;

            // Libellé français
            const levelFr  = { 'Low': 'Niveau faible', 'Moderate': 'Niveau modéré', 'High': 'Niveau élevé', 'Severe': 'Niveau sévère' };
            const levelClass = level.toLowerCase();

            document.getElementById('resultScore').textContent      = level;
            document.getElementById('resultLevel').textContent      = levelFr[level] || level;
            document.getElementById('resultLevel').className        = 'result-level ' + levelClass;
            document.getElementById('resultConfidence').textContent = `Fiabilité du modèle : ${fiab}% de confiance`;

            const result = document.getElementById('predictionResult');
            result.classList.add('visible');

            // Animer la barre
            setTimeout(() => {
                document.getElementById('resultBar').style.width = pct + '%';
            }, 50);

            // Graphique de positionnement
            setTimeout(() => updateResultPositionChart(level), 100);

            // Conseil si High ou Severe
            const advice = document.getElementById('proAdvice');
            advice.style.display = (level === 'High' || level === 'Severe') ? 'block' : 'none';
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