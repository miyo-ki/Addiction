import pandas as pd 

df = pd.read_csv("BDD_initial/addiction_population_data.csv")

#distribution du nombre de cigarettes fumées par jour
dist_cigarettes = df["smokes_per_day"].value_counts().sort_index()
labels_dist = list(dist_cigarettes.index)
data_dist = list(dist_cigarettes.values)


#moyenne par genre
mean_by_gender = df.groupby("gender")["smokes_per_day"].mean()
labels_gender = list(mean_by_gender.index)
data_gender = list(mean_by_gender.values)

#Tranches de consommation
bins = [1, 5, 10, 21]  # 2-5 faible, 6-10 moyen, 11+ élevé
labels_bins = ["Faible","Moyen","Élevé"]
df["consumption_level"] = pd.cut(df["smokes_per_day"], bins=bins, labels=labels_bins)
consumption_counts = df["consumption_level"].value_counts().reindex(labels_bins)
labels_tranches = list(consumption_counts.index)
data_tranches = list(consumption_counts.values)

#Moyenne par niveau académique
mean_by_education = df.groupby("education_level")["smokes_per_day"].mean()
labels_edu = list(mean_by_education.index)
data_edu = [round(x,2) for x in mean_by_education.values]

#âge vs cigarettes/jour
df_scatter = df.dropna(subset=['age', 'smokes_per_day', 'gender'])

# Listes pour JavaScript
scatter_x = df_scatter['age'].tolist()            # âge
scatter_y = df_scatter['smokes_per_day'].tolist() # cigarettes/jour
scatter_colors = df_scatter['gender'].map({
    'Female': 'rgba(244,114,182,0.7)',
    'Male': 'rgba(96,165,250,0.7)',
    'Other': 'rgba(167,139,250,0.7)'
}).tolist()

# Affichage pour copier dans JS
print("Scatter X (âge) :", scatter_x[:20], "...")       # On affiche juste un aperçu
print("Scatter Y (cigarettes/jour) :", scatter_y[:20], "...")
print("Scatter colors :", scatter_colors[:20], "...")

#affichage pour copier dans le code js
print("Distribution (labels/data):")
print(labels_dist)
print(data_dist)

print("\nPar genre (labels/data):")
print(labels_gender)
print(data_gender)

print("\nTranches de consommation (labels/data):")
print(labels_tranches)
print(data_tranches)

print("\nPar niveau académique (labels/data):")
print(labels_edu)
print(data_edu)

