<?php
include '../inc/init.inc.php';
include '../inc/function.inc.php';
if (user_is_admin() == false) {
  header('Location:' . URL . 'connexion.php');
}


//----------------------------------------------------------------
//----------------------------------------------------------------
// Suppression d'un commentaire
//----------------------------------------------------------------
//----------------------------------------------------------------

if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_commentaire'])) {
  // requete delete basée sur l'id_commentaire pour supprimer la commentaire en question
  $supp = $pdo->prepare("DELETE FROM commentaire WHERE id_commentaire = :id_commentaire");
  $supp->bindParam(':id_commentaire', $_GET['id_commentaire'], PDO::PARAM_STR);
  $supp->execute();
}


//----------------------------------------------------------------
//----------------------------------------------------------------
// Fin suppression d'une commentaire
//----------------------------------------------------------------
//----------------------------------------------------------------












//----------------------------------------------------------------
//----------------------------------------------------------------
// LECTURE DE LA TABLE commentaire
//----------------------------------------------------------------
//----------------------------------------------------------------

// recuperation de tous les produits de la bdd
$liste_commentaire = $pdo->query("SELECT commentaire.id_commentaire, pseudo, annonce.titre, commentaire.commentaire, commentaire.date_enregistrement, commentaire.reponses FROM commentaire INNER JOIN annonce AS annonce ON commentaire.annonce_id=annonce.id_annonce INNER JOIN membre AS membre ON commentaire.membre_id=membre.id_membre ORDER BY date_enregistrement");


include '../inc/header.inc.php'; // début des affichages dans la page !
include '../inc/nav.inc.php';
?>
<main class="container">
  <div class="bg-light p-5 rounded">
    <h1><i class="fas fa-ghost"></i> Gestion des commentaires <i class="fas fa-ghost"></i></h1>
    <p class="lead">Bienvenue sur notre site.
      <hr><?php echo $msg; ?>
    </p>
  </div>

  <div class="row">
    <div class="col-12 mt-5">
      <table class="table table-bordered text-center">
        <thead>
          <tr>
            
            <th>Id commentaire</th>
            <th>posté par</th>
            
            <th>Titre de l'annonce</th>
            <th>Question</th>
            <th>posté le</th>
            <th>réponse</th>
            <th>supprimer</th>

          </tr>
        </thead>
        <tbody>
          <?php
          while ($commentaire = $liste_commentaire->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';

            foreach ($commentaire as $indice => $valeur) {

              echo '<td>' . $valeur . '</td>';
            }

            // Rajout du liens pour supprimer
           
            echo '<td><a href="?action=supprimer&id_commentaire=' . $commentaire['id_commentaire'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes vous sùr ?\'))"><i class="fas fa-dumpster-fire"></i></a></td>';
            echo '<tr>';
          }



          ?>
        </tbody>

      </table>

    </div>
  </div>
  
</main>

<?php
include '../inc/footer.inc.php';
