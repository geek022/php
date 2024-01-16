<head>
  <title>Page d'acceuil</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <?php
  include_once('formulaires/entete.html');
  session_start();
  ?>
  <!-- Division principale de la page -->
  <div class="container-fluid mt-3">
  <div class="row">
    <!-- Colonne principale 9/12 de la largeur -->
    <div class="col-md-10">
      <!--Caroussel Bootstrap -->
      <div id="demo" class="carousel slide text-center" data-bs-ride="carousel">
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
          <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
          <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
        </div>
        <!-- Le diaporama de mon caroussel -->
        <h1 class="text-center text-success">Dernières acquisitions</h1>
        <div class="carousel-inner text-center">
          <?php
          require_once('conf/connexion.php');
          //Requête pour obtenir les deux derniers livres ajoutés
          $requete = $connexion->prepare("SELECT * FROM LIVRE ORDER BY dateajout DESC LIMIT 2 ");
          $requete->execute();
          $res = $requete->fetch(PDO::FETCH_OBJ);
          echo '<div class="carousel-item active">';
          echo '<img src="images/' . $res->image . ' "width="70%" height="45%">';
          echo '</div>';
          while ($res = $requete->fetch(PDO::FETCH_OBJ)) {
            echo '<div class="carousel-item">';
            echo '<img src="images/' . $res->image . '"width="70%" height="45%">';
            echo '</div>';
          }
          ?>
        </div>
        <!-- Contrôles/icônes gauches et droite pour le caroussel -->
        <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
        <div class="container-fluid">
          <h3 class="text-center">(Carrousel)</h3>
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