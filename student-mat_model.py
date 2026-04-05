
import joblib
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import LabelEncoder, OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
from sklearn.model_selection import train_test_split


print("fff")
df = pd.read_csv("BDD_initial/student-mat.csv", sep=",", index_col=0)


# Features exactement dans le même ordre que le formulaire PHP
features = ["age", "G1", "G2", "G3", "freetime", "goout",
            "health", "absences", "studytime",
            "Mjob", "Fjob", "reason", "activities", "romantic"]

X = df[features]
y = df["Dalc"]

# Colonnes catégorielles et numériques
cat_cols = ["Mjob", "Fjob", "reason", "activities", "romantic"]
num_cols = ["age", "G1", "G2", "G3", "freetime", "goout",
            "health", "absences", "studytime"]

# Pipeline : encodage + modèle dans un seul objet
preprocessor = ColumnTransformer([
    ("num", "passthrough", num_cols),
    ("cat", OneHotEncoder(handle_unknown="ignore"), cat_cols)
])

pipeline = Pipeline([
    ("preprocessor", preprocessor),
    ("model", RandomForestClassifier(n_estimators=100, random_state=42))
])

X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42
)

pipeline.fit(X_train, y_train)

acc = pipeline.score(X_test, y_test)
print(f"Accuracy : {acc:.4f}")

# ── Sauvegarder le pipeline entier (preprocessing + modèle) ──
joblib.dump(pipeline, "model_alcohol.pkl")
print("Modèle sauvegardé : model_alcohol.pkl")