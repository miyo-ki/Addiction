import numpy as np
import pandas as pd
from sklearn.neighbors import KNeighborsRegressor
from sklearn.model_selection import GridSearchCV
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from sklearn.preprocessing import StandardScaler
from sklearn.pipeline import Pipeline

def run(X_train, X_test, y_train, y_test) -> dict:
    """
    Entraîne, évalue et optimise un KNeighborsRegressor (Version GridSearchCV).
    """

    # ------------------------------------------------------------------ #
    # OPTIMISATION 1 : Normalisation en amont (Pre-scaling)              #
    # Au lieu de recalculer le StandardScaler dans chaque pli de la      #
    # validation croisée, on le fait une seule fois au début.            #
    # ------------------------------------------------------------------ #
    scaler = StandardScaler()
    X_train_scaled = scaler.fit_transform(X_train)
    X_test_scaled = scaler.transform(X_test)

    # ------------------------------------------------------------------ #
    # 1. Modèle par défaut                                               #
    # ------------------------------------------------------------------ #
    # On ajoute n_jobs=-1 même sur le modèle par défaut pour accélérer
    knn_default = KNeighborsRegressor(n_jobs=-1)
    knn_default.fit(X_train_scaled, y_train)
    y_pred_default = knn_default.predict(X_test_scaled)

    default_metrics = {
        'mae' : mean_absolute_error(y_test, y_pred_default),
        'rmse': np.sqrt(mean_squared_error(y_test, y_pred_default)),
        'r2'  : r2_score(y_test, y_pred_default),
    }

    # ------------------------------------------------------------------ #
    # 2. Optimisation exhaustive par GridSearchCV                        #
    # ------------------------------------------------------------------ #
    # Sans le Pipeline, on passe directement les paramètres au KNN
    param_grid = {
        'n_neighbors': [3, 5, 7, 9, 11, 15], 
        'weights'    : ['uniform', 'distance'],
        'metric'     : ['euclidean', 'manhattan'],
        'algorithm': ['kd_tree', 'ball_tree'] 
    }

    # GridSearchCV va tester l'intégralité des combinaisons (6 * 2 * 2 = 24 ici)
    # cv=3 permet de garder un bon compromis temps/fiabilité
    grid_search = GridSearchCV(
        KNeighborsRegressor(n_jobs=-1),
        param_grid=param_grid,
        cv=3,             
        scoring='r2',
        n_jobs=-1
    )
    
    grid_search.fit(X_train_scaled, y_train)

    best_knn   = grid_search.best_estimator_
    y_pred_opt = best_knn.predict(X_test_scaled)

    optimized_metrics = {
        'mae'        : mean_absolute_error(y_test, y_pred_opt),
        'rmse'       : np.sqrt(mean_squared_error(y_test, y_pred_opt)),
        'r2'         : r2_score(y_test, y_pred_opt),
        'best_params': grid_search.best_params_,
    }

    # On recrée le Pipeline final pour respecter le format de sortie attendu
    # et permettre une utilisation facile en production.
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