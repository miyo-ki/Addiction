<?php
    include 'bd.php';
    $bdd = getBD();
    $rep = $bdd->query("SELECT SELECT Dalc, COUNT(*) AS total FROM student_mat");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alcool Étudiants — AddictData</title>
    <link rel="stylesheet" href="styles/style_general.css">
    <link rel="stylesheet" href="styles/style_jdd.css">
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-matrix@2.0.1/dist/chartjs-chart-matrix.min.js"></script>


</head>
<body>

    <p>test</p>
          <?php while ($ligne = $rep->fetch()) { ?>
            <label><input type="checkbox" name="pays" value="<?php echo $ligne; ?>" ><?php echo $ligne; ?></label>
			    <?php } 
			    $rep -> closeCursor(); ?>

    <p>alors</p>


</body>
</html>
