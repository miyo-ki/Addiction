import numpy as np
import pandas as pd
from sklearn.ensemble import RandomForestRegressor 
from sklearn.model_selection import RandomizedSearchCV 
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score

def run(X_train, X_test, y_train, y_test) -> dict:
    rf_default = RandomForestRegressor(random_state=42, n_jobs=-1)
    rf_default.fit(X_train, y_train)
    y_pred_default = rf_default.predict(X_test)

    default_metrics = {
        'mae' : mean_absolute_error(y_test, y_pred_default),
        'rmse': np.sqrt(mean_squared_error(y_test, y_pred_default)),
        'r2'  : r2_score(y_test, y_pred_default),
    }

    param_grid = {
        'n_estimators'     : [100, 200, 300],
        'max_depth'        : [None, 5, 10, 20],
        'min_samples_split': [2, 5, 10],
        'min_samples_leaf' : [1, 2, 4],
    }

    random_search = RandomizedSearchCV(
        RandomForestRegressor(random_state=42, n_jobs=-1),
        param_distributions=param_grid,
        n_iter=15,       # Ne teste que 15 combinaisons au hasard
        cv=3,            
        scoring='r2',
        random_state=42, 
        n_jobs=-1
    )
    random_search.fit(X_train, y_train)

    best_rf = random_search.best_estimator_
    y_pred_opt = best_rf.predict(X_test)

    optimized_metrics = {
        'mae'        : mean_absolute_error(y_test, y_pred_opt),
        'rmse'       : np.sqrt(mean_squared_error(y_test, y_pred_opt)),
        'r2'         : r2_score(y_test, y_pred_opt),
        'best_params': random_search.best_params_, # CORRECTION syntaxe
    }

    feature_names = (
        X_train.columns.tolist() if hasattr(X_train, 'columns') 
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