import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split, cross_val_score, StratifiedKFold
from sklearn.naive_bayes import GaussianNB
from sklearn.model_selection import GridSearchCV, StratifiedKFold, cross_val_score
from sklearn.metrics import (accuracy_score, roc_auc_score, classification_report, confusion_matrix,)
def run(X_train, X_test, Y_train, Y_test) -> dict:
    """
    Entraîne, évalue et optimise un GaussianNB (Naive Bayes)
    
    Paramètres
    ----------
    X_train, X_test : array-like - features (déjà encodées et numériques)
    Y_train, Y_test : array-like - variable cible binaire (0 ou 1)

    Retourne
    --------

    dict avec les clés :
    'défault' -> métriques du modèle par défaut (accuracy, auc, classification_report, confusion_matrix)
    'optimized' -> même métriques que 'défault' + best_params du modèle optimisé
    'cv_scores' -> résultats de la validation croisée (mean +- std)
    'model' -> meilleur estimateur entraîner (GaussianNB)
    """

    #-------------------------------------------------------------------#
    # 1. Modèle par défaut #
    #-------------------------------------------------------------------#

    # apprend la moyenne et la variance de chaque feature pour chaque classe, puis utilise ces statistiques pour calculer les probablités conditionnelles et faire des prédictions.
    gnb_default = GaussianNB()
    gnb_default.fit(X_train, Y_train)

    #predictions et probabilités sur X_test
    Y_pred_default = gnb_default.predict(X_test)#classe : 0 ou 1
    Y_proba_default = gnb_default.predict_proba(X_test)[:, 1]#probablite d'être addicted

    #métrique modèle par défaut
    default_metrics = {
        'accuracy' : accuracy_score(Y_test, Y_pred_default),
        'auc' : roc_auc_score(Y_test, Y_proba_default),
        'classification_report' : classification_report(Y_test, Y_pred_default),
        'confusion_matrix' : confusion_matrix(Y_test, Y_pred_default),
    }

    #-------------------------------------------------------------------#
    # 2. Optimisation par GridSearchCV #
    #-------------------------------------------------------------------#
    # var_smoothing : stabilise la variance des features pour éviter les provabilité nulles. On explore sur une échelle log.
    #sélecrionne la valeur qui maximise l'AUC en validation croisée
    param_grid = {
        'var_smoothing' : np.logspace(-11, -1, 20)
    }

    grid_search = GridSearchCV(
        GaussianNB(),
        param_grid,
        cv = StratifiedKFold(n_splits = 5, shuffle = True, random_state = 42),
        scoring = 'roc_auc',
        n_jobs = -1,
    )

    grid_search.fit(X_train, Y_train)

    #meilleur modèle trouvé par GridSearchCV
    best_gnb = grid_search.best_estimator_
    Y_pred_opt = best_gnb.predict(X_test)
    Y_proba_opt = best_gnb.predict_proba(X_test)[:, 1]

    #métriques optimisées
    optimized_metrics = {
        'accuracy' : accuracy_score(Y_test, Y_pred_opt),
        'auc' : roc_auc_score(Y_test, Y_proba_opt),
        'classification_report' : classification_report(Y_test, Y_pred_opt),
        'confusion_matrix' : confusion_matrix(Y_test, Y_pred_opt),
        'best_params' : grid_search.best_params_,
    }

    # ------------------------------------------------------------------ #
    # 3. Validation croisée sur le meilleur modèle                        #
    # ------------------------------------------------------------------ #

    X_full = np.concatenate([X_train, X_test], axis=0)
    Y_full = np.concatenate([Y_train, Y_test], axis=0)
 
    cv = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
    cv_accuracy = cross_val_score(best_gnb, X_full, Y_full, cv=cv, scoring='accuracy')
    cv_auc = cross_val_score(best_gnb, X_full, Y_full, cv=cv, scoring='roc_auc')
 
    cv_scores = {
        'accuracy_mean': cv_accuracy.mean(),
        'accuracy_std' : cv_accuracy.std(),
        'auc_mean' : cv_auc.mean(),
        'auc_std' : cv_auc.std(),
        'raw_accuracy' : cv_accuracy,
        'raw_auc' : cv_auc,
    }
 
    return {
        'default' : default_metrics,
        'optimized' : optimized_metrics,
        'cv_scores' : cv_scores,
        'model' : best_gnb,
    }