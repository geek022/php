<!DOCTYPE html>
<html lang="fr">

<head>
  <title>Authenification</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Inclusion de Bootstrap CSS-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <?php
  //Inclusion de mon fichier de configuration et cnx à la BDD
  require_once('conf/connexion.php');
  //Je vérifie si l'existence de la session['connecte]
  if (!isset($_SESSION['connecte'])) {
    //Initialisation de ma session après vérification
    $_SESSION['connecte'] = false;
  }
  //Je vérifie l'état de ma session
  if ($_SESSION['connecte'] === true) {
    if ($_SESSION['profil'] !== "membre") {
      echo '<div class="border p-3 text-center">';
      echo '<p >' . $_SESSION['profil'] . ' :'. '</p><br>';
      echo '<p>' . $_SESSION['prenom'] . ' ' . $_SESSION['nom'] . '</p><br>';
      echo '<p>' . $_SESSION['mel'] . '</p><br>';
      echo '<form method="post">
          <input type="submit" class="btn btn-outline-secondary mt-2" value="Se déconnecter" name="deco">
          </form>';
      echo '</div>';
    } else {
      //Affichage des informations de l'utilisateur après connexion
      echo '<div class="border p-3 text-center">';
      echo '<p>' . $_SESSION['prenom'] . ' ' . $_SESSION['nom'] . '</p><br>';
      echo '<p>' . $_SESSION['mel'] . '</p><br>';
      echo '<p>' . $_SESSION['adresse'] . '</p><br>';
      echo '<p>' . $_SESSION['codepostal'] . ' ' . $_SESSION['ville'] . '</p><br>';
      echo '<form method="post">
          <input type="submit" class="btn btn-outline-secondary mt-2" value="Se déconnecter" name="deco">
          </form>';
      echo '</div>';
    }
    //Traitement de la déconnexion, destruction de la session et redirection
    if (isset($_POST['deco'])) {
      session_destroy();
      header("Location:accueil.php");
      exit();
    }
  } else {
    //Je vérifie sur le bouton à été soumis afin de connaître l'état de l'utilisateur
    if (isset($_POST['btn'])) {
      //Vérification des informations lors de la tentative de connexion
      $mail = $_POST['email'];
      $requete = $connexion->prepare("SELECT * FROM UTILISATEUR WHERE mel=:email");
      $requete->bindParam(":email", $mail);
      $requete->execute();
      $resultat = $requete->fetch(PDO::FETCH_OBJ);
      //Vérification du mot de passe
      if (password_verify($_POST['mdp'], $resultat->motdepasse )) {
        $_SESSION['mel'] = $resultat->mel;
        $_SESSION['nom'] = $resultat->nom;
        $_SESSION['prenom'] = $resultat->prenom;
        $_SESSION['adresse'] = $resultat->adresse;
        $_SESSION['codepostal'] = $resultat->codepostal;
        $_SESSION['ville'] = $resultat->ville;
        $_SESSION['profil'] = $resultat->profil;
        $_SESSION['connecte'] = true;
        header("Location:accueil.php");
      } else {
        echo '<p class="float-end">Vous êtes déconnecté</p>';
        $_SESSION['connecte'] = false;
      }
    } else {
      //Formulaire de connexion si aucune tentative de connexion n'a été effectuée
      echo '<div class="border p-3 text-center">';
      echo '<p class="text-center">Se connecter</p>';
      echo '<form method="post">';
      echo '<label for="email">Identifiant</label>';
      echo '<input type="email" class="form-control solid" id="email" placeholder="Enter email" name="email">';
      echo '<label for="pwd">Mot de passe</label>';
      echo '<input type="password" class="form-control solid" id="pwd" placeholder="Enter password" name="mdp"><br>';
      echo '<button type="submit" class="btn btn-outline-secondary solid" name="btn">Connexion</button>';
      echo '</form>';
      echo '</div>';
    }
  }

  ?>
</body>

</html>