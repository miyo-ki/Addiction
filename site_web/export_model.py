"""
export_model.py — À exécuter UNE SEULE FOIS en ligne de commande :
    python3 export_model.py

Entraîne le meilleur modèle (RF ou XGBoost) et sauvegarde :
    model.pkl, encoders.pkl, feature_names.pkl
"""

import os
import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import r2_score, mean_absolute_error, mean_squared_error
import joblib

try:
    from xgboost import XGBRegressor
    XGBOOST_AVAILABLE = True
except ImportError:
    XGBOOST_AVAILABLE = False
    print("XGBoost non disponible, seul Random Forest sera testé.")

# ── Chemins ──────────────────────────────────────────────────────────────────
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
CSV_PATH = os.path.join(BASE_DIR, '..', 'BDD_initial',
                        'Students Social Media Addiction.csv')

# ── Chargement ────────────────────────────────────────────────────────────────
df = pd.read_csv(CSV_PATH)
print(f"Dataset chargé : {df.shape[0]} lignes, {df.shape[1]} colonnes")

# Supprimer Student_ID
df = df.drop(columns=['Student_ID'])

# ── Encodage des variables catégorielles ─────────────────────────────────────
CATEGORICAL_COLS = [
    'Gender', 'Academic_Level', 'Country', 'Most_Used_Platform',
    'Affects_Academic_Performance', 'Relationship_Status'
]

encoders = {}
for col in CATEGORICAL_COLS:
    le = LabelEncoder()
    df[col] = le.fit_transform(df[col].astype(str))
    encoders[col] = le

# ── Features / cible ─────────────────────────────────────────────────────────
TARGET = 'Addicted_Score'
feature_names = [c for c in df.columns if c != TARGET]
X = df[feature_names].values
y = df[TARGET].values

X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42
)

# ── Entraînement et comparaison ───────────────────────────────────────────────
def evaluate(name, model):
    model.fit(X_train, y_train)
    y_pred = model.predict(X_test)
    r2   = r2_score(y_test, y_pred)
    mae  = mean_absolute_error(y_test, y_pred)
    rmse = float(np.sqrt(mean_squared_error(y_test, y_pred)))
    print(f"{name:30s} — R²: {r2:.4f}  MAE: {mae:.4f}  RMSE: {rmse:.4f}")
    return r2, mae, rmse, model

results = {}

rf = RandomForestRegressor(n_estimators=200, random_state=42, n_jobs=-1)
r2, mae, rmse, fitted = evaluate("Random Forest (200 arbres)", rf)
results['Random Forest'] = (r2, mae, rmse, fitted)

if XGBOOST_AVAILABLE:
    xgb = XGBRegressor(n_estimators=200, random_state=42,
                       verbosity=0, eval_metric='rmse')
    r2x, maex, rmsex, fittedx = evaluate("XGBoost (200 estimateurs)", xgb)
    results['XGBoost'] = (r2x, maex, rmsex, fittedx)

# ── Sélection du meilleur modèle (R² max) ────────────────────────────────────
best_name = max(results, key=lambda k: results[k][0])
best_r2, best_mae, best_rmse, best_model = results[best_name]

print(f"\n✓ Meilleur modèle : {best_name}")
print(f"  R²  = {best_r2:.4f}")
print(f"  MAE = {best_mae:.4f}")
print(f"  RMSE= {best_rmse:.4f}")

# ── Sauvegarde ────────────────────────────────────────────────────────────────
joblib.dump(best_model,  os.path.join(BASE_DIR, 'model.pkl'))
joblib.dump(encoders,    os.path.join(BASE_DIR, 'encoders.pkl'))
joblib.dump(feature_names, os.path.join(BASE_DIR, 'feature_names.pkl'))

print("\nFichiers sauvegardés :")
print("  model.pkl, encoders.pkl, feature_names.pkl")
print(f"  Ordre des features : {feature_names}")
