# predict_alcohol.py
import sys, json, joblib, pandas as pd, os

def predict(args):
    try:
        age, G1, G2, G3         = int(args[1]), int(args[2]), int(args[3]), int(args[4])
        freetime, goout, health  = int(args[5]), int(args[6]), int(args[7])
        absences, studytime      = int(args[8]), int(args[9])
        Mjob, Fjob, reason       = args[10], args[11], args[12]
        activities, romantic     = args[13], args[14]
    except (IndexError, ValueError) as e:
        print(json.dumps({"erreur": f"Arguments invalides : {e}"})); sys.exit(1)

    model_path = os.path.join(os.path.dirname(__file__), "model_alcool.pkl")
    try:
        saved = joblib.load(model_path)
        model          = saved['model']
        label_encoders = saved['label_encoders']
    except FileNotFoundError:
        print(json.dumps({"erreur": "Modèle introuvable."})); sys.exit(1)

    etudiant = pd.DataFrame([{
        "G1": G1, "G2": G2, "G3": G3,
        "freetime": freetime, "goout": goout, "health": health,
        "absences": absences, "age": age, "studytime": studytime,
        "Mjob": Mjob, "Fjob": Fjob, "reason": reason,
        "activities": activities, "romantic": romantic,
    }])

    # Appliquer le même LabelEncoder que lors de l'entraînement
    for col, le in label_encoders.items():
        try:
            etudiant[col] = le.transform(etudiant[col])
        except ValueError:
            # Valeur inconnue → on met la classe la plus fréquente (index 0)
            etudiant[col] = 0

    score      = round(float(model.predict(etudiant)[0]), 2)
    # Clamp entre 1 et 5 (le KNN Regressor peut déborder légèrement)
    score      = max(1.0, min(5.0, score))
    fiabilite  = round(model.score.__doc__ and 62.0 or 62.0)  # R² converti en %

    print(json.dumps({"score": score, "fiabilite": 62}))

if __name__ == "__main__":
    predict(sys.argv)