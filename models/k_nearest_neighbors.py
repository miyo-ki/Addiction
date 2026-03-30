import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split, cross_val_score, StratifiedKFold, GridSearchCV
from sklearn.neighbors import KNeighborsClassifier
from sklearn.metrics import (accuracy_score, roc_auc_score, classification_report, confusion_matrix)

def run(X_train, X_test, Y_train, Y_test) -> dict:
    """
    Entraîne, évalue et optimise un KNN (K-Nearest Neighbors)

    Paramètres
    ----------
    X_train, X_test : array-like - features (IMPORTANT : déjà normalisées !)
    Y_train, Y_test : array-like - variable cible binaire (0 ou 1)

    Retourne
    --------
    dict avec :
    'default' -> métriques du modèle par défaut
    'optimized' -> métriques + best_params
    'cv_scores' -> validation croisée
    'model' -> meilleur modèle entraîné
    """

    #-------------------------------------------------------------------#
    # 1. Modèle par défaut #
    #-------------------------------------------------------------------#
    #modèle knn par défaut
    knn_default = KNeighborsClassifier()
    knn_default.fit(X_train, Y_train)

    #prédictions et probabilités sur X_test
    Y_pred_default = knn_default.predict(X_test)
    Y_proba_default = knn_default.predict_proba(X_test)[:, 1]

    #calcul des métriques du modèle par défaut
    default_metrics = {
        'accuracy': accuracy_score(Y_test, Y_pred_default),
        'auc': roc_auc_score(Y_test, Y_proba_default),
        'classification_report': classification_report(Y_test, Y_pred_default),
        'confusion_matrix': confusion_matrix(Y_test, Y_pred_default),
    }

    #-------------------------------------------------------------------#
    # 2. Optimisation avec GridSearchCV #
    #-------------------------------------------------------------------#
    #grille des paramètre à tester pour KNN : nombre de voisins, poids et métrique de distance
    param_grid = {
        'n_neighbors': [3, 5, 7, 9, 11],
        'weights': ['uniform', 'distance'],
        'metric': ['euclidean', 'manhattan']
    }

    #test toutes les combinaisons et selectionnes le meilleur modèle avec validation croisée
    grid_search = GridSearchCV(
        KNeighborsClassifier(),
        param_grid,
        cv=StratifiedKFold(n_splits=5, shuffle=True, random_state=42),
        scoring='roc_auc',
        n_jobs=-1,
    )

    #entrainement + rechezrche des meilleurs paramètres
    grid_search.fit(X_train, Y_train)

    #prédictions du meilleur modèle optimisé
    best_knn = grid_search.best_estimator_

    Y_pred_opt = best_knn.predict(X_test)
    Y_proba_opt = best_knn.predict_proba(X_test)[:, 1]

    #métrique du modèle optimisé
    optimized_metrics = {
        'accuracy': accuracy_score(Y_test, Y_pred_opt),
        'auc': roc_auc_score(Y_test, Y_proba_opt),
        'classification_report': classification_report(Y_test, Y_pred_opt),
        'confusion_matrix': confusion_matrix(Y_test, Y_pred_opt),
        'best_params': grid_search.best_params_,
    }

    #-------------------------------------------------------------------#
    # 3. Validation croisée #
    #-------------------------------------------------------------------#

    #on regroupe train + test pour avoir toutes les données pour la validation croisée du modèle
    X_full = np.concatenate([X_train, X_test], axis=0)
    Y_full = np.concatenate([Y_train, Y_test], axis=0)

    #définition de la validation croisée stratifiée
    cv = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)

    #calcul des scores de validation croisée
    cv_accuracy = cross_val_score(best_knn, X_full, Y_full, cv=cv, scoring='accuracy')
    cv_auc = cross_val_score(best_knn, X_full, Y_full, cv=cv, scoring='roc_auc')

    #résumé des scores de validation croisée
    cv_scores = {
        'accuracy_mean': cv_accuracy.mean(),
        'accuracy_std': cv_accuracy.std(),
        'auc_mean': cv_auc.mean(),
        'auc_std': cv_auc.std(),
        'raw_accuracy': cv_accuracy,
        'raw_auc': cv_auc,
    }

    #résumé des performances
    return {
        'default': default_metrics,
        'optimized': optimized_metrics,
        'cv_scores': cv_scores,
        'model': best_knn,

    }