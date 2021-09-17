<?php
include '../inc/init.inc.php';
include '../inc/function.inc.php';
if (user_is_admin() == false) {
  header('Location:' . URL . 'connexion.php');
}


//----------------------------------------------------------------
//----------------------------------------------------------------
// Suppression d'une note
//----------------------------------------------------------------
//----------------------------------------------------------------

if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_note'])) {
  // requete delete basée sur l'id_commentaire pour supprimer la commentaire en question
  $supp = $pdo->prepare("DELETE FROM note WHERE id_note = :id_note");
  $supp->bindParam(':id_note', $_GET['id_note'], PDO::PARAM_STR);
  $supp->execute();
}


//----------------------------------------------------------------
//----------------------------------------------------------------
// Fin suppression d'une note
//----------------------------------------------------------------
//----------------------------------------------------------------

$id_note = '';
$avis = '';







//----------------------------------------------------------------
//----------------------------------------------------------------
// LECTURE DE LA TABLE note
//----------------------------------------------------------------
//----------------------------------------------------------------

// recuperation de toutes les notes de la bdd
$liste_note = $pdo->query("SELECT note.id_note, pseudo, note.membre_id2, note.note, note.avis, DATE_FORMAT(note.date_enregistrement, '%d/%m/%Y') FROM note INNER JOIN membre AS membre ON note.membre_id1=membre.id_membre ORDER BY note.date_enregistrement");


include '../inc/header.inc.php'; 
include '../inc/nav.inc.php';
?>
<main class="container">
  <div class="bg-light p-5 rounded">
    <h1><i class="fas fa-ghost"></i> Gestion des notes <i class="fas fa-ghost"></i></h1>
    <p class="lead">Bienvenue sur notre site.
      <hr><?php echo $msg; ?>
    </p>
  </div>

  <div class="row">
    <div class="col-12 mt-5">
      <table class="table table-bordered text-center">
        <thead>
          <tr>
            
            <th>Id note</th>
            <th>posté par</th>
            <th>Pour le vendeur</th>
            <th>Note</th>
            <th>Avis</th>
            <th>posté le</th>
            <th>supprimer</th>

          </tr>
        </thead>
        <tbody>
          <?php
          while ($note = $liste_note->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';

            foreach ($note as $indice => $valeur) {
              
              
              if($indice=='membre_id2'){
               $membre = $pdo->query("SELECT pseudo FROM membre WHERE id_membre =".$valeur."");
               $id = $membre->fetch(PDO::FETCH_ASSOC);
               echo '<td>' . $id['pseudo'] . '</td>';
              }elseif($indice!='membre_id2' && $indice!='note'){
                echo '<td>' . $valeur . '</td>';
              }elseif($indice=='note'){
                switch ($valeur){
                  case 1:
                    echo  '<td><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i></td>';
                    break;
                case 2:
                    echo  '<td><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i></td>';
                    break;
                case 3:
                    echo  '<td><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i></td>';
                    break;
                case 4:
                    echo  '<td><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i></td>';
                    break;
                case 5:
                    echo '<td><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></td>';
                    break;

                }
              }
              
            }

            // Rajout du liens pour supprimer
           
            echo '<td><a href="?action=supprimer&id_note=' . $note['id_note'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes vous sùr ?\'))"><i class="fas fa-dumpster-fire"></i></a></td>';
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
