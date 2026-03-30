
import numpy as np
from sklearn.decomposition import PCA
from sklearn.preprocessing import StandardScaler

def ACP(df, n_components=0.95):
    #Normalisation 
    scaler = StandardScaler()
    X = scaler.fit_transform(df) 

    # n_components=0.95 -> conserver 95% de la variance
    pca = PCA(n_components=n_components) 

    data_sortie = pca.fit_transform(X)

    return data_sortie

