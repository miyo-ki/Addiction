import numpy as np
import pandas as pd
from sklearn.neighbors import KNeighborsRegressor
from sklearn.model_selection import RandomizedSearchCV
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from sklearn.preprocessing import StandardScaler
from sklearn.pipeline import Pipeline

def run(X_train, X_test, y_train, y_test) -> dict:
    """
    Entraîne, évalue et optimise un KNeighborsRegressor.
    """

    # ------------------------------------------------------------------ #
    # 1 : Normalisation en amont (Pre-scaling)              #
    # ------------------------------------------------------------------ #
    scaler = StandardScaler()
    X_train_scaled = scaler.fit_transform(X_train)
    X_test_scaled = scaler.transform(X_test)

    # ------------------------------------------------------------------ #
    # Modèle par défaut                                               #
    # ------------------------------------------------------------------ #
    knn_default = KNeighborsRegressor()
    knn_default.fit(X_train_scaled, y_train)
    y_pred_default = knn_default.predict(X_test_scaled)

    default_metrics = {
        'mae' : mean_absolute_error(y_test, y_pred_default),
        'rmse': np.sqrt(mean_squared_error(y_test, y_pred_default)),
        'r2'  : r2_score(y_test, y_pred_default),
    }

    # ------------------------------------------------------------------ #
    # 2. Optimisation par RandomizedSearchCV                             #
    # ------------------------------------------------------------------ #
    param_dist = {
        'n_neighbors': [3, 5, 7, 9, 11, 15], 
        'weights'    : ['uniform', 'distance'],
        'metric'     : ['euclidean', 'manhattan'],
        'algorithm': ['kd_tree', 'ball_tree'] 
    }



