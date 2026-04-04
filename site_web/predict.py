"""
predict.py — Appelé par PHP via shell_exec().
Usage : python3 predict.py <Age> <Gender> <Academic_Level> <Country>
        <Avg_Daily_Usage_Hours> <Most_Used_Platform>
        <Affects_Academic_Performance> <Sleep_Hours_Per_Night>
        <Mental_Health_Score> <Relationship_Status>
        <Conflicts_Over_Social_Media>

Sortie (stdout) : une seule ligne JSON, ex. {"score": 6.2, "fiabilite": 78}
"""

import sys
import os
import json
import numpy as np
import joblib

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

CATEGORICAL_COLS = [
    'Gender', 'Academic_Level', 'Country', 'Most_Used_Platform',
    'Affects_Academic_Performance', 'Relationship_Status'
]

try:
    # ── Chargement des artefacts ──────────────────────────────────────────────
    model         = joblib.load(os.path.join(BASE_DIR, 'model.pkl'))
    encoders      = joblib.load(os.path.join(BASE_DIR, 'encoders.pkl'))
    feature_names = joblib.load(os.path.join(BASE_DIR, 'feature_names.pkl'))

    # ── Lecture des arguments ─────────────────────────────────────────────────
    args = sys.argv[1:]
    if len(args) != len(feature_names):
        raise ValueError(
            f"Attendu {len(feature_names)} arguments, reçu {len(args)}"
        )

    # ── Construction de la ligne de données ──────────────────────────────────
    row = {}
    for name, val in zip(feature_names, args):
        row[name] = val

    # Encodage des colonnes catégorielles
    for col in CATEGORICAL_COLS:
        le  = encoders[col]
        val = str(row[col])
        if val in le.classes_:
            row[col] = int(le.transform([val])[0])
        else:
            # Valeur inconnue : classe la plus fréquente (index 0 après fit)
            row[col] = 0

    # Conversion numérique pour les autres colonnes
    for col in feature_names:
        if col not in CATEGORICAL_COLS:
            row[col] = float(row[col])

    # Tableau numpy dans l'ordre exact du training
    X = np.array([[row[col] for col in feature_names]])

    # ── Prédiction ────────────────────────────────────────────────────────────
    score = round(float(model.predict(X)[0]), 1)
    score = max(2.0, min(9.0, score))

    # ── Fiabilité (std des arbres → % de confiance) ───────────────────────────
    from sklearn.ensemble import RandomForestRegressor
    if isinstance(model, RandomForestRegressor):
        tree_preds = np.array([tree.predict(X)[0] for tree in model.estimators_])
        std = float(np.std(tree_preds))
    else:
        # XGBoost : estimation via bruit résiduel approximatif
        std = 0.5  # valeur par défaut raisonnable

    fiabilite = int(round(max(0.0, 100.0 - std * 20.0)))
    fiabilite = min(100, fiabilite)

    print(json.dumps({"score": score, "fiabilite": fiabilite}))

except Exception as e:
    print(json.dumps({"erreur": str(e)}))
