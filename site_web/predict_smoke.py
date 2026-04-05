import sys
import json
import joblib
import os
import pandas as pd
import numpy as np

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
ROOT_DIR = os.path.dirname(BASE_DIR)  # remonte dans Addiction
sys.path.append(ROOT_DIR)  

DEFAULTS = {
    'id'                        : 0,
    'name'                      : 'Unknown',
    'country'                   : 'France',
    'city'                      : 'Unknown',
    'marital_status'            : 'Single',
    'children_count'            : 0,
    'age_started_drinking'      : 18,
    'attempts_to_quit_drinking' : 0,
    'has_health_issues'         : False,
    'bmi'                       : 25.0,
    'therapy_history'           : 'None',
    'addict_drink'              : 0,
}

try:
    (age, gender, education_level, employment_status,
     annual_income_usd, drinks_per_week, age_started_smoking,
     attempts_to_quit_smoking, mental_health_status,
     exercise_frequency, diet_quality, sleep_hours,
     social_support, addict_smoke) = sys.argv[1:]

    personne = {
        **DEFAULTS,
        'age'                      : int(age),
        'gender'                   : gender,
        'education_level'          : education_level,
        'employment_status'        : employment_status,
        'annual_income_usd'        : int(annual_income_usd),
        'drinks_per_week'          : int(drinks_per_week),
        'age_started_smoking'      : int(age_started_smoking),
        'attempts_to_quit_smoking' : int(attempts_to_quit_smoking),
        'mental_health_status'     : mental_health_status,
        'exercise_frequency'       : exercise_frequency,
        'diet_quality'             : diet_quality,
        'sleep_hours'              : float(sleep_hours),
        'social_support'           : social_support,
        'addict_smoke'             : int(addict_smoke),
    }

    # Chargement
    model           = joblib.load(os.path.join(BASE_DIR, 'model_smoke.pkl'))
    feature_columns = joblib.load(os.path.join(BASE_DIR, 'feature_names_smoke.pkl'))

    # Créer un DataFrame et appliquer OHE
    from models.encoder import oneHotEncoder
    personne_df  = pd.DataFrame([personne])
    personne_ohe = oneHotEncoder(personne_df.copy())

    # Réindexer pour avoir exactement les mêmes colonnes qu'à l'entraînement
    X = personne_ohe.reindex(columns=feature_columns, fill_value=0)

    score = round(float(model.predict(X)[0]), 1)

    if score < 5:
        niveau = "Non-fumeur ou très occasionnel"
    elif score < 15:
        niveau = "Fumeur modéré"
    else:
        niveau = "Fumeur intensif"

    print(json.dumps({'score': score, 'niveau': niveau}))

except Exception as e:
    print(json.dumps({'erreur': str(e)}))