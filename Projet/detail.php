<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail du livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('formulaires/entete.html'); ?>
    <?php
    echo'<div class="container-fluid mt-3">';
    echo '<div class="row">';
    echo'    <div class="col-md-10">';
    if (isset($_GET['nolivre'])) {
        $noauteur = $_GET['nolivre'];
        require_once('conf/connexion.php');
        // AFFICHAGE nolivre récupéré de l'URL
        $_GET['nolivre'];
        $requete = $connexion->prepare("SELECT * FROM AUTEUR A INNER JOIN LIVRE L ON A.noauteur=L.noauteur where L.nolivre=:nolivre");
        $requete->bindParam(":nolivre", $_GET['nolivre']);
        $requete->execute();
        $res = $requete->fetch(PDO::FETCH_OBJ);
        echo '<div class="col-md-6">';
        echo "<h4>Auteur : " . $res->prenom . " " . $res->nom . "</h4><br>";
        echo "<h4>ISBN13 : " . $res->isbn13 . "</h4><br>";
        echo "<h4>$res->titre </h4><br>";
        echo '<h4 class="text-danger">Résumé du livre</h4><br>';
        echo "<br><h4>" . $res->resume . "</h4><br>";
        echo"<h4>Date de parution : ".$res->anneeparution. "</h4><br>";
        echo '</div>';
        echo '<div class="col-md-3">';
        echo '<img src="images/' . $res->image . ' "width="100%" height="75%">';
        echo '</div>';
        echo'</div>';
    }
    echo '<div class="col-md-2">';
    include('authentification.php');
    include_once('session.php');
    echo '</div>';
    echo '</div>';
    echo '<div class="row">';
    echo '<div class="col-md-12">';
    if (isset($_GET['nolivre'])) {
        $livre = $_GET['nolivre'];
        $req = $connexion->prepare("SELECT * from emprunter E INNER JOIN livre L ON L.nolivre=E.nolivre where E.nolivre=:livre");
        $req->bindParam(":livre", $_GET['nolivre']);
        $req->execute();
        $res = $req->fetch(PDO::FETCH_OBJ);
    }
    echo '</div>';
    echo '</div>';
    if (isset($_SESSION['profil']) == 'membre') {
        if (empty($res)) {
            echo '<div class="d-flex">';
            echo '<h4 class="text-success">Disponible  </h4>';
            echo '<div>';
            echo '<form action="panier.php" method="post" >
            <input type="submit" class="btn btn-outline-secondary text-center mx-4" value="Emprunter(ajout au panier)" name="emprunter">
            <a href="panier.php" name="nolivre"></a>
            </form>';
            echo '</div>';
            echo '</div>';
            $_SESSION['emprunter'] = $_GET['nolivre'];
        }else{
            echo'<p class="text-danger">Indisponible</p>';
        }
    } else {
        if (empty($res)) {
            echo '<div class="d-flex">';
            echo '<h4 class="text-success">Disponible  </h4>';
            echo '<h4 class="text-danger mx-3">Pour pouvoir réserver vous dever posséder un compte et vous identifier.</h4>';
            echo '<div>';
        }
    }
    ?>
</body>

</html>