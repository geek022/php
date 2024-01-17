<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fichier de connexion à la BDD</title>
</head>

<body>
  <?php
  // Connexion au serveur
  try {
    $dns = 'mysql:host=localhost;dbname=biblio'; // dbname : nom de la base
    $utilisateur = 'root'; // root sur vos postes
    $motDePasse = ''; // pas de mot de passe sur vos postes
    $connexion = new PDO($dns, $utilisateur, $motDePasse);
  } catch (Exception $e) {
    echo "Connexion à MySQL impossible : ", $e->getMessage();
    die();
  }
  ?>
</body>

</html>