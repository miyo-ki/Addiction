<?php
/* ══════════════════════════════════════════
   social_addiction.php
   Gère aussi l'appel AJAX de prédiction
   ══════════════════════════════════════════ */

// ── AJAX : prédiction ──────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'predict') {
    header('Content-Type: application/json');

    $fields = [
        'age'             => FILTER_VALIDATE_INT,
        'gender'          => FILTER_SANITIZE_SPECIAL_CHARS,
        'academic_level'  => FILTER_SANITIZE_SPECIAL_CHARS,
        'country'         => FILTER_SANITIZE_SPECIAL_CHARS,
        'avg_daily_hours' => FILTER_VALIDATE_FLOAT,
        'platform'        => FILTER_SANITIZE_SPECIAL_CHARS,
        'affects_perf'    => FILTER_SANITIZE_SPECIAL_CHARS,
        'sleep_hours'     => FILTER_VALIDATE_FLOAT,
        'mental_health'   => FILTER_VALIDATE_INT,
        'relationship'    => FILTER_SANITIZE_SPECIAL_CHARS,
        'conflicts'       => FILTER_VALIDATE_INT,
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

    // Chemin vers predict.py — même dossier que social_addiction.php
    $predict_script = __DIR__ . '/predict.py';
    $python         = 'py'; // Windows MAMP

    // predict.py attend les 11 valeurs comme arguments positionnels
    // dans l'ordre exact de feature_names (cf. notebook)
    $args = implode(' ', [
        escapeshellarg((string)$data['age']),
        escapeshellarg($data['gender']),
        escapeshellarg($data['academic_level']),
        escapeshellarg($data['country']),
        escapeshellarg((string)$data['avg_daily_hours']),
        escapeshellarg($data['platform']),
        escapeshellarg($data['affects_perf']),
        escapeshellarg((string)$data['sleep_hours']),
        escapeshellarg((string)$data['mental_health']),
        escapeshellarg($data['relationship']),
        escapeshellarg((string)$data['conflicts']),
    ]);

    // -W ignore supprime les warnings sklearn (version mismatch)
    // qui polluaient stdout et cassaient le JSON
    $cmd    = "$python -W ignore " . escapeshellarg($predict_script) . " $args 2>&1";
    $output = trim(shell_exec($cmd));

    // Extraire uniquement la dernière ligne (le JSON)
    $lines  = explode("\n", $output);
    $last   = trim(end($lines));

    $result = json_decode($last, true);
    if (!$result) {
        echo json_encode(['error' => 'Erreur du modèle', 'raw' => $output]);
    } else {
        // Renommer 'erreur' en 'error' si predict.py a renvoyé une exception
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
    <title>Réseaux Sociaux — AddictData</title>
    <link rel="stylesheet" href="styles/style_general.css">
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
            <li><a href="#presentation">Dataset</a></li>
            <li><a href="#analyses">Analyses</a></li>
            <li><a href="#modele">Modèle IA</a></li>
            <li><a href="#prediction">Prédiction</a></li>
        </ul>
        <span class="nav-badge">Projet Étudiant 2025–2026</span>
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
            <a href="accueil.php#datasets">Jeux de données</a>
            <span class="breadcrumb-sep">/</span>
            <span>Réseaux Sociaux</span>
        </div>
        <div class="dataset-meta-row">
            <span class="dataset-icon-badge">📱</span>
            <span class="dataset-tag-id">01 · Social Addiction</span>
        </div>
        <h1 class="dataset-hero-title">
            Addiction aux<br><em>réseaux sociaux</em>
        </h1>
        <p class="dataset-hero-sub">
            Analyse comportementale de l'usage excessif des plateformes numériques
            et son impact sur la santé mentale de 705 étudiants.
        </p>
        <div class="dataset-hero-pills">
            <span class="hero-pill"><span class="hero-pill-dot"></span>705 étudiants</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>11 variables</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>Régression · Score 2–9</span>
            <span class="hero-pill"><span class="hero-pill-dot"></span>R² = 0.9903</span>
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
        <div class="stat-box"><span class="stat-box-num">705</span><span class="stat-box-label">Étudiants</span></div>
        <div class="stat-box"><span class="stat-box-num">11</span><span class="stat-box-label">Variables</span></div>
        <div class="stat-box"><span class="stat-box-num">0.99</span><span class="stat-box-label">R² meilleur modèle</span></div>
        <div class="stat-box"><span class="stat-box-num">5</span><span class="stat-box-label">Modèles testés</span></div>
    </div>

    <!-- ── 1. PRÉSENTATION DU DATASET ── -->
    <div class="content-block reveal" id="presentation">
        <div class="block-label"><span class="block-label-line"></span>Présentation du dataset</div>
        <h2 class="block-title">Students Social Media Addiction</h2>
        <p class="block-text">
            Ce jeu de données recense <strong>705 étudiants</strong> issus de différents pays et niveaux
            académiques. Il capture leur comportement sur les réseaux sociaux — temps d'usage quotidien,
            plateforme préférée, impact sur les performances scolaires — ainsi que des indicateurs de
            santé mentale et de qualité de vie.
        </p>
        <p class="block-text">
            La <strong>variable cible</strong> est l'<code>Addicted_Score</code>, un score d'addiction
            allant de <strong>2 à 9</strong> qui synthétise le niveau de dépendance numérique de chaque étudiant.
        </p>
        <div class="info-grid">
            <div class="info-card">
                <p class="info-card-title">📊 Variables clés</p>
                <p class="info-card-text">Âge, genre, niveau académique, pays, heures d'usage quotidien, plateforme principale, impact sur les études, heures de sommeil, score de santé mentale, statut relationnel, conflits liés aux réseaux.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">🎯 Variable cible</p>
                <p class="info-card-text">Score d'addiction (2–9). Il s'agit d'un problème de <strong>régression</strong> : on prédit une valeur numérique continue, non une classe binaire.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">🌍 Origine des données</p>
                <p class="info-card-text">Dataset public issu de Kaggle, compilé à partir d'enquêtes menées auprès d'étudiants de plusieurs universités. Utilisé à des fins strictement pédagogiques.</p>
            </div>
            <div class="info-card">
                <p class="info-card-title">⚙️ Preprocessing</p>
                <p class="info-card-text">Variables catégorielles encodées par LabelEncoder et OneHotEncoder. Split 80/20 (564 train / 141 test). ACP testée en complément.</p>
            </div>
        </div>
    </div>

    <!-- ── 2. ANALYSES DESCRIPTIVES ── -->
    <div class="content-block reveal" id="analyses">
        <div class="analysis-section">
            <div class="block-label"><span class="block-label-line"></span>Analyses descriptives</div>
            <h2 class="block-title">Exploration des données</h2>
            <p class="analysis-intro">
                Avant de construire les modèles, nous avons analysé la distribution du score d'addiction,
                les corrélations entre variables et les profils types d'étudiants.
            </p>

            <div class="info-grid">
                <div class="info-card">
                    <p class="info-card-title">📈 Distribution du score</p>
                    <p class="info-card-text">
                        Le score moyen est de <strong>~5.4 / 9</strong>. La distribution est relativement symétrique,
                        avec une légère surreprésentation des scores moyens (4–6). Peu d'étudiants sont
                        en dessous de 3 ou au-dessus de 8.
                    </p>
                </div>
                <div class="info-card">
                    <p class="info-card-title">🔗 Corrélations fortes</p>
                    <p class="info-card-text">
                        Les variables les plus corrélées au score : <strong>heures d'usage quotidien</strong>,
                        <strong>score de santé mentale</strong> (corrélation négative), et
                        <strong>conflits liés aux réseaux sociaux</strong>.
                    </p>
                </div>
                <div class="info-card">
                    <p class="info-card-title">📱 Plateformes</p>
                    <p class="info-card-text">
                        Instagram et TikTok sont associés aux scores d'addiction les plus élevés.
                        LinkedIn et Twitter présentent des scores nettement plus faibles en moyenne.
                    </p>
                </div>
                <div class="info-card">
                    <p class="info-card-title">😴 Sommeil & santé mentale</p>
                    <p class="info-card-text">
                        Les étudiants avec moins de 6h de sommeil et un score de santé mentale faible
                        présentent systématiquement des scores d'addiction plus élevés.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ── 3. MODÈLES IA ── -->
    <div class="content-block reveal" id="modele">
        <div class="block-label"><span class="block-label-line"></span>Modèles d'IA</div>
        <h2 class="block-title">Comparaison des modèles entraînés</h2>
        <p class="block-text">
            Nous avons testé <strong>5 algorithmes de régression</strong> avec différentes stratégies
            d'encodage (LabelEncoder, OneHotEncoder, ACP). Le meilleur modèle retenu est le
            <strong>Random Forest (OHE)</strong> avec un R² de 0.9903.
        </p>

        <div class="models-grid">
            <!-- Random Forest OHE — meilleur -->
            <div class="model-card best">
                <p class="model-name">Random Forest</p>
                <div class="model-metrics">
                    <div class="metric-row">
                        <span class="metric-label">R²</span>
                        <span class="metric-value good">0.9903</span>
                    </div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:99%"></div></div>
                    <div class="metric-row">
                        <span class="metric-label">MAE</span>
                        <span class="metric-value">0.0374</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">RMSE</span>
                        <span class="metric-value">0.1555</span>
                    </div>
                    <div class="metric-row">
                        <span class="metric-label">Encodage</span>
                        <span class="metric-value">OHE</span>
                    </div>
                </div>
            </div>

            <!-- KNN LE -->
            <div class="model-card">
                <p class="model-name">KNN</p>
                <div class="model-metrics">
                    <div class="metric-row"><span class="metric-label">R²</span><span class="metric-value">0.9883</span></div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:98%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.0565</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">0.1710</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">LE</span></div>
                </div>
            </div>

            <!-- XGBoost OHE -->
            <div class="model-card">
                <p class="model-name">XGBoost</p>
                <div class="model-metrics">
                    <div class="metric-row"><span class="metric-label">R²</span><span class="metric-value">0.9854</span></div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:98%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.0593</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">0.1913</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">OHE</span></div>
                </div>
            </div>

            <!-- XGBoost rgs OHE -->
            <div class="model-card">
                <p class="model-name">XGBoost RGS</p>
                <div class="model-metrics">
                    <div class="metric-row"><span class="metric-label">R²</span><span class="metric-value">0.9851</span></div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:98%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.0482</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">0.1933</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">OHE</span></div>
                </div>
            </div>

            <!-- Naive Bayes LE -->
            <div class="model-card">
                <p class="model-name">Naive Bayes</p>
                <div class="model-metrics">
                    <div class="metric-row"><span class="metric-label">R²</span><span class="metric-value">0.9576</span></div>
                    <div class="metric-bar-wrap"><div class="metric-bar" style="width:95%"></div></div>
                    <div class="metric-row"><span class="metric-label">MAE</span><span class="metric-value">0.1229</span></div>
                    <div class="metric-row"><span class="metric-label">RMSE</span><span class="metric-value">0.3257</span></div>
                    <div class="metric-row"><span class="metric-label">Encodage</span><span class="metric-value">LE</span></div>
                </div>
            </div>

            <!-- Fonctionnement -->
            <div class="info-card" style="border-left: none;">
                <p class="info-card-title">🌲 Pourquoi Random Forest ?</p>
                <p class="info-card-text">
                    Le Random Forest construit <strong>plusieurs arbres de décision</strong> sur des
                    sous-ensembles aléatoires des données, puis moyenne leurs prédictions.
                    Il est robuste au surapprentissage et capture bien les interactions non-linéaires
                    entre variables — idéal pour ce type de score comportemental.
                </p>
            </div>
        </div>
    </div>

    <!-- ── 4. PRÉDICTION ── -->
    <div class="content-block reveal" id="prediction">
        <div class="block-label"><span class="block-label-line"></span>Prédiction personnalisée</div>
        <h2 class="block-title">Évalue ton score d'addiction</h2>
        <p class="block-text">
            Renseigne ton profil ci-dessous. Le modèle <strong>Random Forest (R² = 0.9903)</strong>
            prédit ton score d'addiction aux réseaux sociaux sur une échelle de 2 à 9.
        </p>

        <div class="prediction-section">
            <form id="predictionForm">
                <div class="prediction-grid">

                    <div class="form-group">
                        <label class="form-label" for="age">Âge</label>
                        <input class="form-input" type="number" id="age" name="age"
                               min="15" max="35" value="20" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="gender">Genre</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="Male">Homme</option>
                            <option value="Female" selected>Femme</option>
                            <option value="Non-binary">Non-binaire</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="academic_level">Niveau académique</label>
                        <select class="form-select" id="academic_level" name="academic_level">
                            <option value="Undergraduate" selected>Licence</option>
                            <option value="Graduate">Master</option>
                            <option value="High School">Lycée</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="country">Pays</label>
                        <select class="form-select" id="country" name="country">
                            <option value="France" selected>France</option>
                            <option value="USA">USA</option>
                            <option value="UK">UK</option>
                            <option value="India">Inde</option>
                            <option value="Canada">Canada</option>
                            <option value="Australia">Australie</option>
                            <option value="Germany">Allemagne</option>
                            <option value="Brazil">Brésil</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="avg_daily_hours">Heures d'usage quotidien</label>
                        <input class="form-input" type="number" id="avg_daily_hours" name="avg_daily_hours"
                               min="0" max="24" step="0.5" value="3" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="platform">Plateforme principale</label>
                        <select class="form-select" id="platform" name="platform">
                            <option value="Instagram" selected>Instagram</option>
                            <option value="TikTok">TikTok</option>
                            <option value="YouTube">YouTube</option>
                            <option value="Twitter">Twitter / X</option>
                            <option value="Facebook">Facebook</option>
                            <option value="Snapchat">Snapchat</option>
                            <option value="LinkedIn">LinkedIn</option>
                            <option value="WhatsApp">WhatsApp</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="affects_perf">Impact sur les études</label>
                        <select class="form-select" id="affects_perf" name="affects_perf">
                            <option value="Yes" selected>Oui</option>
                            <option value="No">Non</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sleep_hours">Heures de sommeil / nuit</label>
                        <input class="form-input" type="number" id="sleep_hours" name="sleep_hours"
                               min="3" max="12" step="0.5" value="7" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="mental_health">Score santé mentale (1–10)</label>
                        <input class="form-input" type="number" id="mental_health" name="mental_health"
                               min="1" max="10" value="6" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="relationship">Statut relationnel</label>
                        <select class="form-select" id="relationship" name="relationship">
                            <option value="Single" selected>Célibataire</option>
                            <option value="In Relationship">En couple</option>
                            <option value="Complicated">Compliqué</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="conflicts">Conflits liés aux réseaux (nb/semaine)</label>
                        <input class="form-input" type="number" id="conflicts" name="conflicts"
                               min="0" max="10" value="1" required>
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
                <div class="result-label">Score prédit / 9</div>
                <div class="result-level" id="resultLevel">—</div>
                <div class="result-confidence" id="resultConfidence"></div>
                <div class="result-bar-wrap">
                    <div class="result-bar" id="resultBar"></div>
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
            Projet universitaire — IUT Informatique &nbsp;·&nbsp; 2024–2025 &nbsp;·&nbsp;
            Données à usage strictement pédagogique.
        </p>
    </div>
</footer>

<script>
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
        const response = await fetch('social_addiction.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.error) {
            document.getElementById('predictionError').textContent = 'Erreur : ' + data.error;
            document.getElementById('predictionError').style.display = 'block';
        } else {
            const score   = parseFloat(data.score).toFixed(2);
            const pct     = ((score - 2) / 7 * 100).toFixed(0);
            const r2 = data.fiabilite !== undefined ? data.fiabilite : '99';

            // Niveau
            let level = '', levelClass = '';
            if (score < 4.5)      { level = 'Niveau faible';  levelClass = 'low'; }
            else if (score < 6.5) { level = 'Niveau modéré';  levelClass = 'medium'; }
            else                   { level = 'Niveau élevé';   levelClass = 'high'; }

            document.getElementById('resultScore').textContent      = score;
            document.getElementById('resultLevel').textContent      = level;
            document.getElementById('resultLevel').className        = 'result-level ' + levelClass;
            document.getElementById('resultConfidence').textContent = `Fiabilité du modèle : ${r2}% de confiance`;

            const result = document.getElementById('predictionResult');
            result.classList.add('visible');

            // Animer la barre
            setTimeout(() => {
                document.getElementById('resultBar').style.width = pct + '%';
            }, 50);
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