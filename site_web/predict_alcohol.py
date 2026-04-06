# predict_alcohol.py
import sys
import json
import joblib
import pandas as pd
import os

def predict(args):
    # ── 1. Lire les arguments dans le même ordre que PHP les envoie ──
    # Ordre : age G1 G2 G3 freetime goout health absences studytime
    #         Mjob Fjob reason activities romantic
    try:
        age        = int(args[1])
        G1         = int(args[2])
        G2         = int(args[3])
        G3         = int(args[4])
        freetime   = int(args[5])
        goout      = int(args[6])
        health     = int(args[7])
        absences   = int(args[8])
        studytime  = int(args[9])
        Mjob       = args[10]
        Fjob       = args[11]
        reason     = args[12]
        activities = args[13]
        romantic   = args[14]
    except (IndexError, ValueError) as e:
        print(json.dumps({"erreur": f"Arguments invalides : {e}"}))
        sys.exit(1)

    # ── 2. Charger le modèle ──
    model_path = os.path.join(os.path.dirname(__file__), "model_alcohol.pkl")
    try:
        pipeline = joblib.load(model_path)
    except FileNotFoundError:
        print(json.dumps({"erreur": "Modèle introuvable. Lancez d'abord le notebook."}))
        sys.exit(1)

    # ── 3. Construire le DataFrame avec les mêmes colonnes qu'à l'entraînement ──
    etudiant = pd.DataFrame([{
        "age"        : age,
        "G1"         : G1,
        "G2"         : G2,
        "G3"         : G3,
        "freetime"   : freetime,
        "goout"      : goout,
        "health"     : health,
        "absences"   : absences,
        "studytime"  : studytime,
        "Mjob"       : Mjob,
        "Fjob"       : Fjob,
        "reason"     : reason,
        "activities" : activities,
        "romantic"   : romantic,
    }])

    # ── 4. Prédiction ──
    score = int(pipeline.predict(etudiant)[0])

    # Probabilités pour chaque classe (optionnel — donne la "fiabilité")
    probas = pipeline.predict_proba(etudiant)[0]
    fiabilite = round(float(max(probas)) * 100, 1)

    # ── 5. Retourner le JSON (toujours sur la dernière ligne) ──
    print(json.dumps({
        "score"     : score,
        "fiabilite" : fiabilite
    }))

if __name__ == "__main__":
    predict(sys.argv)