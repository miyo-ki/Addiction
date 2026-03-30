import pandas as pd

from sklearn.preprocessing import LabelEncoder
from sklearn.preprocessing import OneHotEncoder



def labelEncoder(df):

    categorical_cols = df.select_dtypes(include='object').columns

    label_encoders = {}
    for col in categorical_cols:
        le = LabelEncoder()
        df[col] = le.fit_transform(df[col])
        label_encoders[col] = le

    return df


def oneHotEncoder(df):
    
    categorical_cols = df.select_dtypes(include='object').columns

    # sparse_output=False permet d'avoir un tableau numpy classique
    # handle_unknown='ignore' évite les erreurs si une catégorie apparaît dans le test mais pas dans le train
    encoder = OneHotEncoder(sparse_output=False, handle_unknown='ignore')

    categorical_data = df[categorical_cols]
    encoded_data = encoder.fit_transform(categorical_data)

    #On crée un nouveau DataFrame avec les noms de colonnes générés (ex: 'Mjob_teacher', 'Mjob_health', etc.)
    encoded_df = pd.DataFrame(encoded_data, columns=encoder.get_feature_names_out(categorical_cols), index=df.index)

    #On fusionne avec les colonnes numériques d'origine
    data_final = pd.concat([df.drop(categorical_cols, axis=1), encoded_df], axis=1)
    
    return data_final





