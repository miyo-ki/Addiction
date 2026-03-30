import numpy as np
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import GridSearchCV
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score


def run(X_train, X_test, y_train, y_test) -> dict:
    """
    Entraîne, évalue et optimise un RandomForestRegressor.

    Paramètres
    ----------
    X_train, X_test : array-like - features (déjà encodées et numériques)
    y_train, y_test : array-like - variable cible numérique

    Retourne
    --------
    dict avec les clés :
        'default'            → métriques du modèle par défaut (mae, rmse, r2)
        'optimized'          → métriques + best_params du modèle optimisé
        'feature_importances'→ pd.Series triée par importance décroissante
        'model'              → meilleur estimateur entraîné (GridSearchCV)
    """

    # ------------------------------------------------------------------ #
    # 1. Modèle par défaut                                                 #
    # ------------------------------------------------------------------ #
    rf_default = RandomForestClassifier(random_state=42, n_jobs=-1)
    rf_default.fit(X_train, y_train)
    y_pred_default = rf_default.predict(X_test)

    default_metrics = {
        'mae' : mean_absolute_error(y_test, y_pred_default),
        'rmse': np.sqrt(mean_squared_error(y_test, y_pred_default)),
        'r2'  : r2_score(y_test, y_pred_default),
    }

    # ------------------------------------------------------------------ #
    # 2. Optimisation par GridSearchCV                                     #
    # ------------------------------------------------------------------ #
    param_grid = {
        'n_estimators'     : [100, 200, 300],
        'max_depth'        : [None, 5, 10, 20],
        'min_samples_split': [2, 5, 10],
        'min_samples_leaf' : [1, 2, 4],
    }

    grid_search = GridSearchCV(
        RandomForestClassifier(random_state=42, n_jobs=-1),
        param_grid,
        cv=5,
        scoring='r2',
        n_jobs=-1,
    )
    grid_search.fit(X_train, y_train)

    best_rf = grid_search.best_estimator_
    y_pred_opt = best_rf.predict(X_test)

    optimized_metrics = {
        'mae'        : mean_absolute_error(y_test, y_pred_opt),
        'rmse'       : np.sqrt(mean_squared_error(y_test, y_pred_opt)),
        'r2'         : r2_score(y_test, y_pred_opt),
        'best_params': grid_search.best_params_,
    }

    # ------------------------------------------------------------------ #
    # 3. Importance des variables                                          #
    # ------------------------------------------------------------------ #
    feature_names = (
        X_train.columns.tolist()
        if hasattr(X_train, 'columns')
        else list(range(X_train.shape[1]))
    )
    feature_importances = pd.Series(
        best_rf.feature_importances_,
        index=feature_names
    ).sort_values(ascending=False)

    return {
        'default'            : default_metrics,
        'optimized'          : optimized_metrics,
        'feature_importances': feature_importances,
        'model'              : best_rf,
    }
