<?php
// Démarrage de la session
session_start();
// Vérification si l'utilisateur est un administrateur, sinon je le redirige vers la page d'accueil
if (!isset($_SESSION['profil']) || $_SESSION['profil'] !== 'admin') {
    header('Location:accueil.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un membre - Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid mt-3">
        <div class="row">
            <?php
            require_once('conf/connexion.php');
            $message = ' ';
            // Tableau pour stocker les messages d'erreur par champ
            $erreurs = array();
            if (isset($_POST['membre'])) {
                // Récupération des données du formulaire
                $mel = $_POST['mel'];
                $mdp = $_POST['motdepasse'];
                $nom = $_POST['nom'];
                $prenom = $_POST['prenom'];
                $adresse = $_POST['adresse'];
                $ville = $_POST['ville'];
                $cp = $_POST['codepostal'];
                $profil = "membre";
                //Contrôle de saisie
                if (empty($mel) || !filter_var($mel, FILTER_VALIDATE_EMAIL)) {
                    $erreurs['mel'] = 'Le mel est vide ou invalide.';
                } elseif (empty($mdp) || strlen($mdp) < 4) {
                    $erreurs['motdepasse'] = 'Le mot de passe est vide ou invalide.';
                } elseif (empty($nom) || empty($prenom) || empty($adresse) || empty($ville) || empty($cp)) {
                    $erreurs['champs'] = 'Tous les champs sont obligatoires.';
                } elseif (!is_numeric($cp) || strlen($cp) !== 5) {
                    $erreurs['codepostal'] = 'Le code postal est invalide.';
                }
                if (empty($erreurs)) {
                    //CNX A LA BDD
                    $ajouter_Membre = $connexion->prepare('INSERT INTO UTILISATEUR (mel,motdepasse,nom,prenom,adresse,ville,codepostal,profil) VALUES(:mel,:motdepasse,:nom,:prenom,:adresse,:ville,:codepostal,:profil)');
                    $ajouter_Membre->bindParam(':mel', $mel);
                    $ajouter_Membre->bindParam(':motdepasse', $mdp);
                    $ajouter_Membre->bindParam(':nom', $nom);
                    $ajouter_Membre->bindParam(':prenom', $prenom);
                    $ajouter_Membre->bindParam(':adresse', $adresse);
                    $ajouter_Membre->bindParam(':ville', $ville);
                    $ajouter_Membre->bindParam(':codepostal', $cp);
                    $ajouter_Membre->bindParam(':profil', $profil);
                    //Excution de la requête d'insertion
                    if ($ajouter_Membre->execute()) {
                        $message = '<p>Le membre a été ajouté avec succès.</p>';
                    } else {
                        $message = "<p>Erreur lors de l'ajout du membre.</p>";
                    }
                }
            }

            ?>
            <div class="col-md-8">
                <h2>La Bibliothèque de Moulinsart est fermée au public jusqu'à nouvel ordre. Mais il vous est possible de réserver et retirer vos livres via notre service Biblio Drive !</h2>
                <div class="border p-3">
                    <form method="post" action="menu_admin.php">
                        <button class="btn" type="submit" name="ajouter">Ajouter un livre</button>
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
                    <form method="post">
                        <h1 class="text-danger text-center">Créer un membre</h1>
                        <div class="mb-3">
                            <label for="mel" class="form-label">Mel: </label>
                            <input type="text" class="form-control" name="mel" required>
                            <?php
                            if (isset($erreurs['mel'])) echo '<span class="text-danger">' . $erreurs['mel'] . '</span>';
                            ?>
                        </div>
                        <div class="mb-3">
                            <label for="motdepasse" class="form-label"> Mot de passe: </label>
                            <input type="text" class="form-control" name="motdepasse" required>
                            <?php
                            if (isset($erreurs['motdepasse'])) echo '<span class="text-danger">' . $erreurs['motdepasse'] . '</span>';
                            ?>
                        </div>
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom: </label>
                            <input type="text" class="form-control" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom: </label>
                            <input type="text" class="form-control" name="prenom" required>
                        </div>
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse: </label>
                            <input type="text" class="form-control" name="adresse" required>
                        </div>
                        <div class="mb-3">
                            <label for="ville" class="form-label">Ville: </label>
                            <input type="text" class="form-control" name="ville" required>
                        </div>
                        <div class="mb-3">
                            <label for="codepostal" class="form-label">Code Postal: </label>
                            <input type="text" class="form-control" name="codepostal" required>
                            <?php
                            if (isset($erreurs['codepostal'])) echo '<span class="text-danger">' . $erreurs['codepostal'] . '</span>';
                            ?>
                        </div>
                        <div>
                            <input type="submit" class="btn btn-secondary" value="Créer un membre" name="membre">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-2 ">
                <?php include_once('authentification.php') ?>
            </div>
        </div>
    </div>
</body>

</html>