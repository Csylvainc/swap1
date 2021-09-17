<?php
include '../inc/init.inc.php';
include '../inc/function.inc.php';
if (user_is_admin() == false) {
  header('Location:' . URL . 'connexion.php');
}


//----------------------------------------------------------------
//----------------------------------------------------------------
// Suppression d'une catégorie
//----------------------------------------------------------------
//----------------------------------------------------------------

if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_categorie'])) {
  // requete delete basée sur l'id_categorie pour supprimer la categorie en question
  $supp = $pdo->prepare("DELETE FROM categorie WHERE id_categorie = :id_categorie");
  $supp->bindParam(':id_categorie', $_GET['id_categorie'], PDO::PARAM_STR);
  $supp->execute();
}


//----------------------------------------------------------------
//----------------------------------------------------------------
// Fin suppression d'une categorie
//----------------------------------------------------------------
//----------------------------------------------------------------

$id_categorie = '';
$titre = '';
$motscles = '';




//----------------------------------------------------------------
//----------------------------------------------------------------
// Modification d'une catégorie
//----------------------------------------------------------------
//----------------------------------------------------------------

if (isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_categorie'])) {
  // Pour la modification d'un article il faut proposer à l'utilisateur les données déjà enregistrées afin qu'il ne change que la ou les valeurs qu'il souhaite
  // Une requete pour récupérer les infos de cette article, un fetch et on affiche dasn le form via les variables déjà en place dans le form
  $modification = $pdo->prepare('SELECT * FROM categorie WHERE id_categorie = :id_categorie');
  $modification->bindParam(':id_categorie', $_GET['id_categorie'], PDO::PARAM_STR);
  $modification->execute();

  $infos_categorie = $modification->fetch(PDO::FETCH_ASSOC);
  $id_categorie = $infos_categorie['id_categorie'];
  $titre = $infos_categorie['titre'];
  $motscles = $infos_categorie['motscles'];
 
}


//----------------------------------------------------------------
//----------------------------------------------------------------
// Fin modification d'une categorie
//----------------------------------------------------------------
//----------------------------------------------------------------


//----------------------------------------------------------------
//----------------------------------------------------------------
// ENREGISTREMENT ET MODIFICATION DES catégories
//----------------------------------------------------------------
//----------------------------------------------------------------


if (
  isset($_POST['titre']) &&
  isset($_POST['motscles']) 
  

) {

  $titre = trim($_POST['titre']);
  $motscles = trim($_POST['motscles']);
  


  $erreur = false;
  // Modification
  // on verifie si l'id_categorie existe est n'est pas vide si true on est en modification
  if (!empty($_POST['id_categorie'])) {
    $id_categorie  = trim($_POST['id_categorie']);
  }



  // Controle sur le titre categorie (non vide et pas en double)
  if (empty($titre)) {
    $erreur = true;
    $msg .= '<div class="alert alert-danger mb-3">Attention le titre de la catégorie est obligatoire!</div>';
  }

  $verif_titre = $pdo->prepare("SELECT * FROM categorie Where titre = :titre");
  $verif_titre->bindParam(':titre', $titre, PDO::PARAM_STR);
  $verif_titre->execute();

  // il ne faut pas verifier si la pseudo existe dans le cadre d'une modif donc on ajoute un controle sur id_categorie vide ou non
  if ($verif_titre->rowCount() > 0 && empty($id_categorie)) {
    $erreur = true;
    $msg .= '<div class="alert alert-danger mb-3">Attention ce titre de catégorie est deja présent!</div>';
  }




  // Enregistrement en BDD
  if ($erreur == false) {

    if (empty($id_categorie)) {
      // Si $id_categorie est vide : INSERT
      $enregistrement = $pdo->prepare("INSERT INTO categorie (titre, motscles) VALUES (:titre, :motscles)");
    } else {
      // Si $id_categorie n'est pas vide : UPDATE
      $enregistrement = $pdo->prepare("UPDATE categorie SET titre = :titre, motscles = :motscles WHERE id_categorie = :id_categorie");

      $enregistrement->bindParam(':id_categorie', $id_categorie, PDO::PARAM_STR);
    }

    $enregistrement->bindParam(':titre', $titre, PDO::PARAM_STR);
    $enregistrement->bindParam(':motscles', $motscles, PDO::PARAM_STR);
    
    $enregistrement->execute();
  }
} // fin des controle champ formulaire
//----------------------------------------------------------------
//----------------------------------------------------------------
//  FIN ENREGISTREMENT ET MODIFICATION DES catégories
//----------------------------------------------------------------
//----------------------------------------------------------------


//----------------------------------------------------------------
//----------------------------------------------------------------
// LECTURE DE LA TABLE catégorie
//----------------------------------------------------------------
//----------------------------------------------------------------

// recuperation de tous les produits de la bdd
$liste_categorie = $pdo->query("SELECT id_categorie, titre, motscles FROM categorie ORDER BY titre");


include '../inc/header.inc.php'; // début des affichages dans la page !
include '../inc/nav.inc.php';
?>
<main class="container">
  <div class="bg-light p-5 rounded">
    <h1><i class="fas fa-ghost"></i> Gestion des catégories <i class="fas fa-ghost"></i></h1>
    <p class="lead">Bienvenue sur notre site.
      <hr><?php echo $msg; ?>
    </p>
  </div>

  <div class="row">
    <div class="col-12 mt-5">
      <table class="table table-bordered text-center">
        <thead>
          <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Mots Cles</th>
            <th>Modifier</th>
            <th>Supprimer</th>
          </tr>
        </thead>
        <tbody>
          <?php
          while ($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';

            foreach ($categorie as $indice => $valeur) {

              echo '<td>' . $valeur . '</td>';
            }

            // Rajout de deux liens pour modif et supprime
            echo '<td><a href="?action=modifier&id_categorie=' . $categorie['id_categorie'] . '" class="btn btn-danger"><i class="fas fa-edit"></i></a></td>';
            echo '<td><a href="?action=supprimer&id_categorie=' . $categorie['id_categorie'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes vous sùr ?\'))"><i class="fas fa-dumpster-fire"></i></a></td>';
            echo '<tr>';
          }



          ?>
        </tbody>

      </table>

    </div>
  </div>
  <div class="col-12 mt-5 border p-3">
    <form class="row border p-3" method="post">
      <div class="col-sm-6 offset-sm-3">
        <div class="mb-3">
          <label for="titre" class="form-label">Titre <i class="fas fa-user-alt"></i></label>
          <input type="text" class="form-control" id="titre" name="titre" value="<?php echo $titre ?>">
        </div>
        <div class="mb-3">
          <label for="mostcles" class="form-label">Mots Clés <i class="fas fa-user-alt"></i></label>
          <input type="text" class="form-control" id="motscles" name="motscles">
        </div>
        <div class="mb-3 mt-4">
          <button type="submit" class="btn btn-outline-secondary w-100" id="inscription"><i class="fas fa-keyboard"></i> Ajouter <i class="fas fa-keyboard"></i></button>
        </div>
      </div>

    </form>
  </div>
  </div>
</main>

<?php
include '../inc/footer.inc.php';
