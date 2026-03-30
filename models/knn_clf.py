import numpy as np
import pandas as pd
from sklearn.model_selection import StratifiedKFold, RandomizedSearchCV, cross_validate
from sklearn.neighbors import KNeighborsClassifier
from sklearn.metrics import accuracy_score, roc_auc_score, classification_report, confusion_matrix

def run(X_train, X_test, Y_train, Y_test) -> dict:
    """
    Entraînenement de KNN 
    """
    # Stratégie de validation commune pour tout le script
    cv_strat = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)

    #-------------------------------------------------------------------#
    # 1. Modèle par défaut 
    #-------------------------------------------------------------------#
    knn_default = KNeighborsClassifier()
    knn_default.fit(X_train, Y_train)

    Y_pred_default = knn_default.predict(X_test)
    Y_proba_default = knn_default.predict_proba(X_test)[:, 1]

    default_metrics = {
        'accuracy': accuracy_score(Y_test, Y_pred_default),
        'auc': roc_auc_score(Y_test, knn_default.predict_proba(X_test), multi_class='ovr', average='macro'),        'classification_report': classification_report(Y_test, Y_pred_default),
        'classification_report': classification_report(Y_test, Y_pred_default),
        'confusion_matrix': confusion_matrix(Y_test, Y_pred_default),
    }

    #-------------------------------------------------------------------#
    # 2. Recherche des hyperparamètres
    #-------------------------------------------------------------------#
    param_dist = {
        'n_neighbors': [3, 5, 7, 9, 11, 15],
        'weights': ['uniform', 'distance'],
        'metric': ['euclidean', 'manhattan']
    }

    random_search = RandomizedSearchCV(
        KNeighborsClassifier(),
        param_distributions=param_dist,
        n_iter=20, 
        cv=cv_strat,
        scoring='roc_auc_ovr_weighted', # <--- Utilise cette version pour le multi-classe
        random_state=42
    )

    random_search.fit(X_train, Y_train)
    best_knn = random_search.best_estimator_

    Y_pred_opt = best_knn.predict(X_test)
    Y_proba_opt = best_knn.predict_proba(X_test)[:, 1]

    optimized_metrics = {
        'accuracy': accuracy_score(Y_test, Y_pred_opt),
        'auc': roc_auc_score(Y_test, best_knn.predict_proba(X_test), multi_class='ovr', average='macro'),
        'classification_report': classification_report(Y_test, Y_pred_opt),
        'confusion_matrix': confusion_matrix(Y_test, Y_pred_opt),
        'best_params': random_search.best_params_,
    }

    #-------------------------------------------------------------------#
    # 3. Validation croisée 
    #-------------------------------------------------------------------#
    # ATTENTION METHODOLOGIE : Normalement, on ne met pas X_test ici. 
    # X_test doit rester totalement invisible pour le modèle. 
    # Je laisse X_train + X_test selon ta demande, mais sois prudent !
    X_full = np.concatenate([X_train, X_test], axis=0)
    Y_full = np.concatenate([Y_train, Y_test], axis=0)

    # OPTIMISATION : cross_validate calcule tout en UNE SEULE passe
    cv_results = cross_validate(
        best_knn, 
        X_full, 
        Y_full, 
        cv=cv_strat, 
        scoring=['accuracy', 'roc_auc'], # On demande les deux métriques
    )

    cv_scores = {
        'accuracy_mean': cv_results['test_accuracy'].mean(),
        'accuracy_std': cv_results['test_accuracy'].std(),
        'auc_mean': cv_results['test_roc_auc'].mean(),
        'auc_std': cv_results['test_roc_auc'].std(),
        'raw_accuracy': cv_results['test_accuracy'],
        'raw_auc': cv_results['test_roc_auc'],
    }

    return {
        'default': default_metrics,
        'optimized': optimized_metrics,
        'cv_scores': cv_scores,
        'model': best_knn,
    }