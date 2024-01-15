<?php
//Démarrage de la session
session_start();
//Restriction de la page si l'utilisateur est un membre
if (isset($_SESSION['profil']) && $_SESSION['profil'] == 'membre') {
    //L'utilisateur avec le profil membre sera rédirigé sur la page d'acceuil
    header('Location:accueil.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un livre - Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <?php
                    require_once('conf/connexion.php');
                    //Je vérifie si le bouton "Ajouter" a été soumis
                    $afficherFormulaire = false;
                    if (isset($_POST['ajouter'])) {
                        $afficherFormulaire = true;
                    }
                    // Tableau pour stocker les messages d'erreur par champ
                    $erreurs = array();
                    // Si le formulaire d'ajout de livre est soumis
                    if (isset($_POST['ajouter_livre'])) {
                        //Je vérifie si les clés existe dans le tableau et je les récupère sinon la fonction retourne null
                        $auteur = isset($_POST['auteur']) ? $_POST['auteur'] : null;
                        $titre = isset($_POST['titre']) ? $_POST['titre'] : null;
                        $isbn13 = isset($_POST['isbn13']) ? $_POST['isbn13'] : null;
                        $anneeparution = isset($_POST['anneeparution']) ? $_POST['anneeparution'] : null;
                        $resume = isset($_POST['resume']) ? $_POST['resume'] : null;
                        $dateajout = date('Y-m-d H:i:s');
                        $image = isset($_POST['image']) ? $_POST['image'] : null;
                        //Contrôle de saisie
                        if (empty($titre)) {
                            $erreurs['titre'] = 'Le titre est vide ou invalide.';
                        } elseif (empty($isbn13) || !is_numeric($isbn13)) {
                            $erreurs['isbn13'] = 'Le code ISBN13 est vide ou invalide.';
                        } elseif (empty($anneeparution) || !is_numeric($anneeparution)) {
                            $erreurs['anneeparution'] = "L'année de parution est vide ou invalide.";
                        } elseif (empty($resume)) {
                            $erreurs['resume'] = 'Le résumé est vide ou invalide.';
                        } elseif (empty($image)) {
                            $erreurs['image'] = "L'image est vide ou invalide.";
                        }
                        if (empty($erreurs)) {
                            // Ajouter le livre à la BDD
                            $requeteAjoutLivre = $connexion->prepare("INSERT INTO LIVRE (noauteur, titre, isbn13, anneeparution, resume,dateajout, image) VALUES (:noauteur,:titre, :isbn13, :anneeparution, :resume,:dateajout, :image)");
                            $requeteAjoutLivre->bindParam(':noauteur', $auteur);
                            $requeteAjoutLivre->bindParam(':titre', $titre);
                            $requeteAjoutLivre->bindParam(':isbn13', $isbn13);
                            $requeteAjoutLivre->bindParam(':anneeparution', $anneeparution);
                            $requeteAjoutLivre->bindParam(':resume', $resume);
                            $requeteAjoutLivre->bindParam(':dateajout', $dateajout);
                            $requeteAjoutLivre->bindParam(':image', $image);
                            if ($requeteAjoutLivre->execute()) {
                                echo '<p>Livre ajouté avec succès.</p>';
                            } else {
                                echo "<p>Erreur lors de l'ajout du livre.</p>";
                            }
                        }
                    }

                    ?>
                    <div class="col-md-8">
                        <h2>La Bibliothèque de Moulinsart est fermée au public jusqu'à nouvel ordre. Mais il vous est possible de réserver et retirer vos livres via notre service Biblio Drive !</h2>
                        <div class="border p-3">
                            <form method="post">
                                <!--Le bouton permettant la redirection sur la page admin pour ajouter des livres-->
                                <button type="submit" class="btn" name="ajouter">Ajouter un livre</button>
                                <!--Lien pour être rédirigé sur ma page membre.php afin de créer un membre-->
                                <a href="membre.php" class="btn" type="button" name="membre">Créer un membre</a>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4 p-3 text-white align-content-lg-end text-end">
                        <img src="formulaires/images3.png" alt="images3" style="max-width: 100%;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-10 d-flex justify-content-center text-center align-items-center">
                        <div class="col-md-5">
                            <h1 class="text-danger text-center">Ajouter un livre</h1>
                            <?php
                            // Afficher les erreurs si elles existent
                            if ($afficherFormulaire) {
                            ?>
                                <form method="post">
                                    <div class="mb-3">
                                        <label for="auteur" class="form-label">Auteur :</label>
                                        <select class="form-select" name="auteur" required>
                                            <?php
                                            require_once('conf/connexion.php');
                                            // Récupération des auteurs existants dans la BDD
                                            $requeteAuteurs = $connexion->query("SELECT * FROM auteur");
                                            while ($auteur = $requeteAuteurs->fetch(PDO::FETCH_OBJ)) {
                                                echo "<option value='$auteur->noauteur'>$auteur->nom $auteur->prenom</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="titre" class="form-label">Titre :</label>
                                        <input type="text" class="form-control" name="titre" required>
                                        <?php
                                        if (isset($erreurs['titre'])) echo '<span class="text-danger">' . $erreurs['titre'] . '</span>';
                                        ?>
                                    </div>
                                    <div class="mb-3">
                                        <label for="isbn13" class="form-label">ISBN13 :</label>
                                        <input type="text" class="form-control" name="isbn13" required>
                                        <?php
                                        if (isset($erreurs['isbn13'])) echo '<span class="text-danger">' . $erreurs['isbn13'] . '</span>';
                                        ?>
                                    </div>
                                    <div class="mb-3">
                                        <label for="anneeparution" class="form-label">Année de parution :</label>
                                        <input type="text" class="form-control" name="anneeparution" required>
                                        <?php
                                        if (isset($erreurs['anneeparution'])) echo '<span class="text-danger">' . $erreurs['anneeparution'] . '</span>';
                                        ?>
                                    </div>
                                    <div class="mb-3">
                                        <label for="resume" class="form-label">Résumé :</label>
                                        <textarea class="form-control" rows="3" id="resume" name="resume" required></textarea>
                                        <?php
                                        if (isset($erreurs['resume'])) echo '<span class="text-danger">' . $erreurs['resume'] . '</span>';
                                        ?>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image :</label>
                                        <input type="text" class="form-control" name="image" required>
                                        <?php
                                        if (isset($erreurs['image'])) echo '<span class="text-danger">' . $erreurs['image'] . '</span>';
                                        ?>
                                    </div>
                                    <input type="submit" class="btn btn-secondary" value="Ajouter" name="ajouter_livre">
                                <?php
                            }
                                ?>
                                </form>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <?php include_once('authentification.php') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>