<?php
//Inclusion du fichier d'entête HTML
include_once('formulaires/entete.html');
//Démarrage de la session
session_start();
//Je vérifie si la session['panier'] existe et si ce n'est pas un tableau
if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = array();
}
//Le déclaration du maximal d'emprunts autorisés
$nombreEmpruntsMax = 5;
//Calcul du nombre d'emprunts restants
$empruntsEncours = $nombreEmpruntsMax - count($_SESSION['panier']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Panier</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-10">
                <h1 class="text-center text-success">Votre panier </h1><br>
                <?php
                //J'affiche un message sur le nombre d'emprunts restants
                if ($empruntsEncours == 1) {
                    echo '<p class="text-primary text-center">(encore 1 réservation possible, ' . $empruntsEncours . ' emprunt en cours)</p>';
                } else {
                    echo '<p class="text-primary text-center">(encore ' . $empruntsEncours . ' réservation possibles, ' . $empruntsEncours . ' emprunts en cours)</p>';
                }
                ?>
                <?php
                require_once('conf/connexion.php');
                //Je vérifie si le formulaire d'emrpunt a été soumis
                if (isset($_POST['emprunter'])) {
                    if (isset($_SESSION['emprunter'])) {
                        if (isset($_SESSION['panier']) && is_array($_SESSION['panier'])) {
                            //Vérification du nombre d'emprunts restants
                            if ($empruntsEncours > 0) {
                                //Je récupère les détails du livre à emrpunter
                                $requete = $connexion->prepare("SELECT * FROM LIVRE WHERE nolivre =:emprunter ");
                                $requete->bindParam(":emprunter", $_SESSION['emprunter']);
                                $requete->execute();
                                $resultat = $requete->fetch(PDO::FETCH_OBJ);
                                //Création d'un tableau associatif pour représenter le livre
                                $tableaux = array(
                                    'nolivre' => $resultat->nolivre,
                                    'noauteur' => $resultat->noauteur,
                                    'Titre' => $resultat->titre,
                                    'ISBN13' => $resultat->isbn13,
                                    'Annee de parution' => $resultat->anneeparution,
                                    'Résumé' => $resultat->resume,
                                    "Date d'ajout" => $resultat->dateajout,
                                    'Image' => $resultat->image,
                                );
                                //Si le panier n'existe pas, je le crée
                                if (!isset($_SESSION['panier'])) {
                                    $_SESSION['panier'] = array();
                                }
                                if (!in_array($tableaux, $_SESSION['panier'])) {
                                    array_push($_SESSION["panier"], $tableaux);
                                    //Je décremente le nombre d'emprunts restants
                                    $empruntsEncours--;
                                } else {
                                    echo '<p class="text-danger text-center">Ce livre existe déja dans votre panier</p>';
                                }
                            } else {
                                echo '<p class="text-danger text-center">Vous avez atteint la limite d\'emprunts en cours (5).</p>';
                            }
                        }
                    }
                }
                //Affichage des livres dans le panier
                foreach ($_SESSION['panier'] as $index => $livreDansPanier) {
                    $requete = $connexion->prepare("SELECT * FROM LIVRE L INNER JOIN AUTEUR A ON (L.noauteur=A.noauteur) WHERE L.nolivre=:livre");
                    $requete->bindParam(":livre", $livreDansPanier['nolivre']);
                    $requete->execute();
                    echo '<div class="d-flex justify-content-center mb-2">';
                    $res = $requete->fetch(PDO::FETCH_OBJ);
                    echo '<div class="d-flex">';
                    echo '<p>' . $res->nom . " " . $res->prenom . " - " . $res->titre . " (" . $res->anneeparution . ")" . '</p>';
                    echo '<form method="post">';
                    echo '<input type="hidden" name="nolivre" value="' . $index . '">';
                    echo '<input type="submit" class="btn btn-secondary ms-2" name="annuler" value="Annuler">';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }
                //Suppression d'un livre panier lorsqu'on clique sur le bouton "Annuler"
                if (isset($_POST['annuler'])) {
                    $index = $_POST['nolivre'];
                    if ($_SESSION['panier'][$index]) {
                        unset($_SESSION['panier'][$index]);
                        $_SESSION['panier'] = array_values($_SESSION['panier']);
                    }
                    header('Location:panier.php');
                    exit();
                }

                //Je vérifie si le panier a été validé
                if (isset($_POST['panier'])) {
                    $connexion->beginTransaction();
                    try {
                        foreach ($_SESSION['panier'] as $livre_emprunter) {
                            //J'insère les livres empruntés dans la BDD
                            $stmt = $connexion->prepare("INSERT INTO EMPRUNTER (mel,nolivre,dateemprunt) VALUES (:mel,:nolivre, NOW())");
                            $stmt->bindParam(":mel", $_SESSION['mel']);
                            $stmt->bindParam(":nolivre", $livre_emprunter['nolivre']);
                            if (!$stmt->execute()) {
                                throw new Exception("Erreur lors de l'insertion du livre");
                            }
                        }
                        $connexion->commit();
                        //Je remets le panier à vide après validation
                        $_SESSION['panier'] = array();
                        echo '<p class="text-success">Le panier a été validé avec succès.</p>';
                    } catch (Exception $e) {
                        $connexion->rollBack();
                        echo '<p class="text-danger">Une erreur est survenue lors de la validation du panier.</p>';
                    }
                    header('Location:panier.php');
                    exit();
                }
                ?>
                <div class="col-md-12 d-flex justify-content-center text-center align-items-center">
                    <div class="col-md-10">
                        <form action="panier.php" method="POST">
                            <input type="submit" value="Valider le panier" name="panier">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <?php
                include_once('authentification.php');
                include_once('session.php');
                ?>
            </div>
        </div>
</body>

</html>