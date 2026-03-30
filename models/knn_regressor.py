import numpy as np
import pandas as pd
from sklearn.neighbors import KNeighborsRegressor
from sklearn.model_selection import GridSearchCV
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from sklearn.preprocessing import StandardScaler
from sklearn.pipeline import Pipeline


def run(X_train, X_test, y_train, y_test) -> dict:
    """
    Entraîne, évalue et optimise un KNeighborsRegressor.

    IMPORTANT : KNN est sensible à l'échelle des features.
    On inclut un StandardScaler dans un Pipeline pour normaliser
    automatiquement avant chaque fit/predict.

    Paramètres
    ----------
    X_train, X_test : array-like - features (déjà encodées et numériques)
    y_train, y_test : array-like - variable cible numérique (régression)

    Retourne
    --------
    dict avec les clés :
        'default'             → métriques du modèle par défaut (mae, rmse, r2)
        'optimized'           → métriques + best_params du modèle optimisé
        'feature_importances' → None (KNN ne fournit pas d'importances)
        'model'               → meilleur Pipeline entraîné (scaler + knn)
    """

    # ------------------------------------------------------------------ #
    # 1. Modèle par défaut                                                 #
    # ------------------------------------------------------------------ #
    pipe_default = Pipeline([
        ('scaler', StandardScaler()),
        ('knn',    KNeighborsRegressor()),
    ])
    pipe_default.fit(X_train, y_train)
    y_pred_default = pipe_default.predict(X_test)

    default_metrics = {
        'mae' : mean_absolute_error(y_test, y_pred_default),
        'rmse': np.sqrt(mean_squared_error(y_test, y_pred_default)),
        'r2'  : r2_score(y_test, y_pred_default),
    }

    # ------------------------------------------------------------------ #
    # 2. Optimisation par GridSearchCV                                     #
    # ------------------------------------------------------------------ #
    # Les clés du param_grid suivent la convention Pipeline : 'étape__paramètre'
    param_grid = {
        'knn__n_neighbors': [3, 5, 7, 9, 11],
        'knn__weights'    : ['uniform', 'distance'],
        'knn__metric'     : ['euclidean', 'manhattan'],
    }

    grid_search = GridSearchCV(
        Pipeline([('scaler', StandardScaler()), ('knn', KNeighborsRegressor())]),
        param_grid,
        cv=5,
        scoring='r2',
        n_jobs=-1,
    )
    grid_search.fit(X_train, y_train)

    best_pipe  = grid_search.best_estimator_
    y_pred_opt = best_pipe.predict(X_test)

    optimized_metrics = {
        'mae'        : mean_absolute_error(y_test, y_pred_opt),
        'rmse'       : np.sqrt(mean_squared_error(y_test, y_pred_opt)),
        'r2'         : r2_score(y_test, y_pred_opt),
        'best_params': grid_search.best_params_,
    }

    return {
        'default'            : default_metrics,
        'optimized'          : optimized_metrics,
        'feature_importances': None,
        'model'              : best_pipe,
    }