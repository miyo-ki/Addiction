import numpy as np
import pandas as pd
<<<<<<< HEAD:models/knn_regressor.py
from sklearn.neighbors import KNeighborsRegressor
from sklearn.model_selection import RandomizedSearchCV
=======
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import GridSearchCV
>>>>>>> 887a6bb80332cb69c0db1790e3ba9dd01e5d0254:models/random_forest_clf.py
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score

def run(X_train, X_test, y_train, y_test) -> dict:
    """
<<<<<<< HEAD:models/knn_regressor.py
    Entraîne, évalue et optimise un KNeighborsRegressor.
=======
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
>>>>>>> 887a6bb80332cb69c0db1790e3ba9dd01e5d0254:models/random_forest_clf.py
    """

    # ------------------------------------------------------------------ #
    # 1 : Normalisation en amont (Pre-scaling)              #
    # ------------------------------------------------------------------ #
<<<<<<< HEAD:models/knn_regressor.py
    scaler = StandardScaler()
    X_train_scaled = scaler.fit_transform(X_train)
    X_test_scaled = scaler.transform(X_test)

    # ------------------------------------------------------------------ #
    # Modèle par défaut                                               #
    # ------------------------------------------------------------------ #
    knn_default = KNeighborsRegressor()
    knn_default.fit(X_train_scaled, y_train)
    y_pred_default = knn_default.predict(X_test_scaled)
=======
    rf_default = RandomForestClassifier(random_state=42, n_jobs=-1)
    rf_default.fit(X_train, y_train)
    y_pred_default = rf_default.predict(X_test)
>>>>>>> 887a6bb80332cb69c0db1790e3ba9dd01e5d0254:models/random_forest_clf.py

    default_metrics = {
        'mae' : mean_absolute_error(y_test, y_pred_default),
        'rmse': np.sqrt(mean_squared_error(y_test, y_pred_default)),
        'r2'  : r2_score(y_test, y_pred_default),
    }

    # ------------------------------------------------------------------ #
    # 2. Optimisation par RandomizedSearchCV                             #
    # ------------------------------------------------------------------ #
<<<<<<< HEAD:models/knn_regressor.py
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
=======
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
>>>>>>> 887a6bb80332cb69c0db1790e3ba9dd01e5d0254:models/random_forest_clf.py
        scoring='r2',
        random_state=42   # Pour la reproductibilité
    )

<<<<<<< HEAD:models/knn_regressor.py
    random_search.fit(X_train_scaled, y_train)

    best_knn   = random_search.bestestimator
    y_pred_opt = best_knn.predict(X_test_scaled)
=======
    best_rf = grid_search.best_estimator_
    y_pred_opt = best_rf.predict(X_test)
>>>>>>> 887a6bb80332cb69c0db1790e3ba9dd01e5d0254:models/random_forest_clf.py

    optimized_metrics = {
        'mae'        : mean_absolute_error(y_test, y_pred_opt),
        'rmse'       : np.sqrt(mean_squared_error(y_test, y_pred_opt)),
        'r2'         : r2_score(y_test, y_pred_opt),
        'best_params': random_search.bestparams,
    }

<<<<<<< HEAD:models/knn_regressor.py
    final_pipeline = Pipeline([
        ('scaler', scaler),
        ('knn', best_knn)
    ])
=======
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
>>>>>>> 887a6bb80332cb69c0db1790e3ba9dd01e5d0254:models/random_forest_clf.py

    return {
        'default'            : default_metrics,
        'optimized'          : optimized_metrics,
<<<<<<< HEAD:models/knn_regressor.py
        'feature_importances': None,
        'model'              : final_pipeline,
    }
=======
        'feature_importances': feature_importances,
        'model'              : best_rf,
    }
>>>>>>> 887a6bb80332cb69c0db1790e3ba9dd01e5d0254:models/random_forest_clf.py
