import numpy as np
import pandas as pd
from xgboost import XGBRegressor
from sklearn.model_selection import GridSearchCV
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score

def run(X_train, X_test, y_train, y_test) -> dict:
    
    xgb_default = XGBRegressor(
        n_estimators=100,
        max_depth=6,
        learning_rate=0.1,
        random_state=42,
    )

    xgb_default.fit(X_train, y_train)
    y_pred_default = xgb_default.predict(X_test)

    default_metrics = {
        'mae' : mean_absolute_error(y_test, y_pred_default),
        'rmse' : np.sqrt(mean_squared_error(y_test, y_pred_default)),
        'r2' : r2_score(y_test, y_pred_default),
    }

    param_grid = {
        'n_estimators' : [50, 100, 200],
        'max_depth' : [3, 5, 6, 10],
        'learning_rate' : [0.05, 0.1, 0.2],
        'subsample' : [0.8, 1.0],
    }

    grid_search = GridSearchCV(
        XGBRegressor(random_state=42),
        param_grid,
        cv=5,
        scoring='r2',
        n_jobs=-1,
    )
    grid_search.fit(X_train, y_train)
 
    best_xgb = grid_search.best_estimator_
    y_pred_opt = best_xgb.predict(X_test)
 
    optimized_metrics = {
        'mae'        : mean_absolute_error(y_test, y_pred_opt),
        'rmse'       : np.sqrt(mean_squared_error(y_test, y_pred_opt)),
        'r2'         : r2_score(y_test, y_pred_opt),
        'best_params': grid_search.best_params_,
    }

    feature_names = (
        X_train.columns.tolist()
        if hasattr(X_train, 'columns')
        else list(range(X_train.shape[1]))
    )
 
    feature_importances = pd.Series(
        best_xgb.feature_importances_,
        index=feature_names
    ).sort_values(ascending=False)
 
    return {
        'default'            : default_metrics,
        'optimized'          : optimized_metrics,
        'feature_importances': feature_importances,
        'model'              : best_xgb,
    }