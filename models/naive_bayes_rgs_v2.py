import numpy as np
import pandas as pd
from sklearn.naive_bayes import GaussianNB
from sklearn.model_selection import GridSearchCV
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score

def run(X_train, X_test, y_train, y_test) -> dict:
    def predict_regression(model, X):
        classes = model.classes_.astype(float)
        proba   = model.predict_proba(X)
        return proba @ classes

    # 1. Modèle par défaut
    gnb_default = GaussianNB()
    gnb_default.fit(X_train, y_train)
    y_pred_default = predict_regression(gnb_default, X_test)

    default_metrics = {
        'mae' : mean_absolute_error(y_test, y_pred_default),
        'rmse': np.sqrt(mean_squared_error(y_test, y_pred_default)),
        'r2'  : r2_score(y_test, y_pred_default),
    }

    # 2. Optimisation rapide 
    param_grid = {'var_smoothing': np.logspace(-11, -5, 10)}

    grid_search = GridSearchCV(
        GaussianNB(),
        param_grid,
        cv=3,
        scoring='r2',
        n_jobs=-1,
    )
    grid_search.fit(X_train, y_train)

    best_gnb   = grid_search.best_estimator_
    y_pred_opt = predict_regression(best_gnb, X_test)

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
        'model'              : best_gnb,
    }