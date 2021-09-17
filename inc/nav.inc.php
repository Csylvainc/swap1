<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo URL; ?>index.php">SWAP</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav me-auto mb-2 mb-md-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?php echo URL; ?>index.php">Annonces</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo URL; ?>depos_annonce.php">Déposer / modifier une annonce</a>
        </li>

        <?php if (user_is_connected() == false) { ?>

          <li class="nav-item">
            <a class="nav-link" href="<?php echo URL; ?>connexion.php">Connexion</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URL; ?>inscription.php">Inscription</a>
          </li>

        <?php } else { ?>

          <li class="nav-item">
            <a class="nav-link" href="<?php echo URL; ?>connexion.php">Profil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URL; ?>connexion.php?action=deconnexion">Deconnexion</a>
          </li>

        <?php } ?>

        <?php if(user_is_admin() == true) { ?>

        <div class="dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
           Administration
          </a>

          <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <li><a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_annonce.php">Gestion des annonces</a></li>
            <li><a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_membre.php">Gestion des membres</a></li>
            <li><a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_categorie.php">Gestion des catégories</a></li>
            <li><a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_commentaire.php">Gestion des commentaires</a></li>
            <li><a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_note.php">Gestion des notes</a></li>
            <li><a class="dropdown-item" href="<?php echo URL; ?>admin/statistique">Statistique</a></li>
          </ul>
        </div>
        <?php } ?>
      </ul>
      <form class="d-flex" method="get" action="<?php echo URL; ?>index.php">
      
      <label for="myDataList" class="form-label">Chercher un produit :</label>
        <input class="form-control" list="datalistOptions" id="myDataList" placeholder="Type to search..." name="recherche">
        <datalist id="datalistOptions">
        </datalist>
        <button class="btn btn-outline-success" type="submit">Rechercher</button> -->
     </form>
     <div>
     <?php if(user_is_connected() == true) { ?>
     <?php echo '<span class="bg-light h3">Bonjour '.$_SESSION['membre']['pseudo'].'</span>'; ?></div>
     <?php } ?>
    </div>
  </div>
  <script src="assets/js/app.js"></script>
</nav>