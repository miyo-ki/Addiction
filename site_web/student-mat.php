<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Addiction</title>
    <link rel="stylesheet" href="styles/style_student-mat.css">
</head>
<body>
    
    <div class="title_section">
        <div class="bouton-menu" style="cursor: pointer; padding: 15px; font-weight: bold; text-align: center;">
            Afficher / Masquer le jeu de données 1
        </div>
        <div class="conteneur">
            <h1 class="dataset-title">Addiction à l'alcool des étudiants, avec notes en math</h1>
            <p class="dataset-description">Étude épidémiologique sur la consommation d'alcool, tabac et drogues dans différentes tranches d'âge.</p>
            <div class="dataset-meta">
                <span class="dataset-entries">4 812 entrées</span>
                <div class="dataset-tags">
                    <span class="tag">Épidémiologie</span>
                    <span class="tag">Substances</span>
                    <span class="tag">Âge</span>
                </div>
            </div>
        </div>

        <div class="bouton-menu" style="cursor: pointer; padding: 15px; font-weight: bold; text-align: center;">
            Afficher / Masquer le jeu de données 2 
        </div>
        <div class="conteneur">
            <h1 class="dataset-title">Autre étude sur les étudiants</h1>
            <p class="dataset-description">Une autre description pour le deuxième jeu de données.</p>
            <div class="dataset-meta">
                <span class="dataset-entries">2 150 entrées</span>
                <div class="dataset-tags">
                    <span class="tag">Épidémiologie</span>
                    <span class="tag">Santé</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // On récupère TOUS les boutons qui ont la classe 'bouton-menu'
        const boutonsMenu = document.querySelectorAll('.bouton-menu');

        // On boucle sur chaque bouton trouvé
        boutonsMenu.forEach(bouton => {
            bouton.addEventListener('click', function() {
                // "this" représente le bouton sur lequel on vient de cliquer
                // nextElementSibling va chercher l'élément HTML juste en dessous de ce bouton (donc son conteneur)
                const conteneurAssocie = this.nextElementSibling;
                
                // On affiche ou masque uniquement ce conteneur précis
                conteneurAssocie.classList.toggle('afficher');
            });
        });

        // On empêche la fermeture si on clique à l'intérieur de n'importe quel conteneur
        const tousLesConteneurs = document.querySelectorAll('.conteneur');
        tousLesConteneurs.forEach(conteneur => {
            conteneur.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        });
    </script>

</body>
</html>