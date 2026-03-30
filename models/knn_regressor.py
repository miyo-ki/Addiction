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

    # ------------------------------------------------------------------ #
    # 3 : RandomizedSearch
    # On cherche parmi un échantillon aléatoire de la grille
    # ------------------------------------------------------------------ #
    random_search = RandomizedSearchCV(
        KNeighborsRegressor(),
        param_distributions=param_dist,
        n_iter=20,        # Nombre de combinaisons à tester (sur les 24 possibles)
        cv=4,
        scoring='r2',
        random_state=42   # Pour la reproductibilité
    )

    random_search.fit(X_train_scaled, y_train)

    best_knn   = random_search.bestestimator
    y_pred_opt = best_knn.predict(X_test_scaled)

    optimized_metrics = {
        'mae'        : mean_absolute_error(y_test, y_pred_opt),
        'rmse'       : np.sqrt(mean_squared_error(y_test, y_pred_opt)),
        'r2'         : r2_score(y_test, y_pred_opt),
        'best_params': random_search.bestparams,
    }

    final_pipeline = Pipeline([
        ('scaler', scaler),
        ('knn', best_knn)
    ])

    return {
        'default'            : default_metrics,
        'optimized'          : optimized_metrics,
        'feature_importances': None,
        'model'              : final_pipeline,
    }