# fastapi_predict.py
# Démarrage : python -m uvicorn fastapi_predict:app --reload
#
# Pipeline : OHE -> StandardScaler -> PCA(20) -> GaussianNB
# Features : toutes colonnes de student-mat.csv sauf Dalc et Walc (28 variables)

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import joblib
import pandas as pd
import os

app = FastAPI(title="AddictData — Alcohol Prediction API")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["POST"],
    allow_headers=["*"],
)

MODEL_PATH = os.path.join(os.path.dirname(__file__), "model_alcohol.pkl")
try:
    MODEL = joblib.load(MODEL_PATH)
    print("Modele charge (OHE -> Scaler -> PCA -> NaiveBayes)")
except FileNotFoundError:
    MODEL = None
    print("ERREUR : model_alcohol.pkl introuvable")


class StudentData(BaseModel):
    age:        int
    Medu:       int
    Fedu:       int
    traveltime: int
    studytime:  int
    failures:   int
    famrel:     int
    freetime:   int
    goout:      int
    health:     int
    absences:   int
    G1:         int
    G2:         int
    G3:         int
    school:     str
    sex:        str
    address:    str
    famsize:    str
    Pstatus:    str
    Mjob:       str
    Fjob:       str
    reason:     str
    guardian:   str
    schoolsup:  str
    famsup:     str
    paid:       str
    activities: str
    nursery:    str
    higher:     str
    internet:   str
    romantic:   str


@app.get("/")
def health():
    return {"status": "ok", "model_loaded": MODEL is not None}


@app.post("/predict")
def predict(data: StudentData):
    if MODEL is None:
        return {"error": "Modele non charge. Verifiez model_alcohol.pkl."}

    etudiant = pd.DataFrame([data.model_dump()])

    score = int(MODEL.predict(etudiant)[0])
    score = max(1, min(5, score))

    try:
        probas    = MODEL.predict_proba(etudiant)[0]
        fiabilite = round(float(max(probas)) * 100, 1)
    except AttributeError:
        fiabilite = 60.0

    return {"score": score, "fiabilite": fiabilite}
