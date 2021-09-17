<?php
include '../inc/init.inc.php';
include '../inc/function.inc.php';
if (user_is_admin() == false) {
  header('Location:' . URL . 'connexion.php');
}

//------------------------------
//-------------------------------
// Modification du statut d'un membre
//-------------------------------
//-------------------------------
if (isset($_GET['id']))
{
  $valeur= addslashes($_GET['valeur']);
  $update = $pdo->prepare("update membre set ".$valeur."=:new where id_membre=:id");
  //$update->bindParam(':valeur', $_GET['valeur'], PDO::PARAM_STR);
  $update->bindParam(':new', $_GET['new'], PDO::PARAM_STR);
  $update->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
  $update->execute();
}
//------------------------------
//-------------------------------
// FIN Modification du statut d'un membre
//-------------------------------
//-------------------------------


//----------------------------------------------------------------
//----------------------------------------------------------------
// Suppression d'un membre
//----------------------------------------------------------------
//----------------------------------------------------------------

if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_membre'])) {
  // requete delete basée sur l'id_annonce pour supprimer l'article en question pour
  $supp = $pdo->prepare("DELETE FROM membre WHERE id_membre = :id_membre");
  $supp->bindParam(':id_membre', $_GET['id_membre'], PDO::PARAM_STR);
  $supp->execute();
}


//----------------------------------------------------------------
//----------------------------------------------------------------
// Fin suppression d'un membre
//----------------------------------------------------------------
//----------------------------------------------------------------

$id_membre = '';
$pseudo = '';
$mdp = '';
$nom = '';
$prenom = '';
$telephone = '';
$email = '';
$civilite = '';
$date_enregistrement = '';



//----------------------------------------------------------------
//----------------------------------------------------------------
// Modification d'un membre
//----------------------------------------------------------------
//----------------------------------------------------------------

if (isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_membre'])) {
  // Pour la modification d'un article il faut proposer à l'utilisateur les données déjà enregistrées afin qu'il ne change que la ou les valeurs qu'il souhaite
  // Une requete pour récupérer les infos de cette article, un fetch et on affiche dasn le form via les variables déjà en place dans le form
  $modification = $pdo->prepare('SELECT * FROM membre WHERE id_membre = :id_membre');
  $modification->bindParam(':id_membre', $_GET['id_membre'], PDO::PARAM_STR);
  $modification->execute();

  $infos_membre = $modification->fetch(PDO::FETCH_ASSOC);
  $id_membre = $infos_membre['id_membre'];
  $pseudo = $infos_membre['pseudo'];
  $nom = $infos_membre['nom'];
  $prenom = $infos_membre['prenom'];
  $telephone = $infos_membre['telephone'];
  $email = $infos_membre['email'];
  $civilite = $infos_membre['civilite'];
  $date_enregistrement = $infos_membre['date_enregistrement'];
}


//----------------------------------------------------------------
//----------------------------------------------------------------
// Fin modification d'un membre
//----------------------------------------------------------------
//----------------------------------------------------------------


//----------------------------------------------------------------
//----------------------------------------------------------------
// ENREGISTREMENT ET MODIFICATION DES membres
//----------------------------------------------------------------
//----------------------------------------------------------------


if (
  isset($_POST['pseudo']) &&
  isset($_POST['nom']) &&
  isset($_POST['prenom']) &&
  isset($_POST['email']) &&
  isset($_POST['telephone']) &&
  isset($_POST['civilite'])

) {

  $pseudo = trim($_POST['pseudo']);
  $nom = trim($_POST['nom']);
  $prenom = trim($_POST['prenom']);
  $email = trim($_POST['email']);
  $telephone = trim($_POST['telephone']);
  $civilite = trim($_POST['civilite']);


  $erreur = false;
  // Modification
  // on verifie si l'id_membre existe est n'est pas vide si true on est en modification
  if (!empty($_POST['id_membre'])) {
    $id_membre  = trim($_POST['id_membre']);
  }



  // Controle sur le pseudo (non vide et pas en double)
  if (empty($pseudo)) {
    $erreur = true;
    $msg .= '<div class="alert alert-danger mb-3">Attention le pseudo est obligatoire!</div>';
  }

  $verif_pseudo = $pdo->prepare("SELECT * FROM membre Where pseudo = :pseudo");
  $verif_pseudo->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
  $verif_pseudo->execute();

  // il ne faut pas verifier si la pseudo existe dans le cadre d'une modif donc on ajoute un controle sur id_membre vide ou non
  if ($verif_pseudo->rowCount() > 0 && empty($id_membre)) {
    $erreur = true;
    $msg .= '<div class="alert alert-danger mb-3">Attention cette reference est deja présente!</div>';
  }




  // Enregistrement en BDD
  if ($erreur == false) {

    if (empty($id_membre)) {
      // Si $id_membre est vide : INSERT
      $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, telephone, email, civilite, statut, date_enregistrement) VALUES (:pseudo, :mdp, :nom, :prenom, :telephone, :email, :civilite, 1, NOW())");
      $enregistrement->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $enregistrement->bindParam(':mdp', $mdp, PDO::PARAM_STR);
    $enregistrement->bindParam(':nom', $nom, PDO::PARAM_STR);
    $enregistrement->bindParam(':prenom', $prenom, PDO::PARAM_STR);
    $enregistrement->bindParam(':telephone', $telephone, PDO::PARAM_STR);
    $enregistrement->bindParam(':email', $email, PDO::PARAM_STR);
    $enregistrement->bindParam(':civilite', $civilite, PDO::PARAM_STR);
    $enregistrement->execute();
    } else {
      // Si $id_membre n'est pas vide : UPDATE
      $enregistrement = $pdo->prepare("UPDATE membre SET pseudo = :pseudo, nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, civilite = :civilite WHERE id_membre = :id_membre");

      $enregistrement->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
    }

    
  }
} // fin des controle champ formulaire
//----------------------------------------------------------------
//----------------------------------------------------------------
//  FIN ENREGISTREMENT ET MODIFICATION DES membres
//----------------------------------------------------------------
//----------------------------------------------------------------


//----------------------------------------------------------------
//----------------------------------------------------------------
// LECTURE DE LA TABLE membre
//----------------------------------------------------------------
//----------------------------------------------------------------

// recuperation de tous les produits de la bdd
$liste_membre = $pdo->query("SELECT id_membre, pseudo, nom, prenom, email, telephone, civilite, statut, date_enregistrement FROM membre ORDER BY date_enregistrement, pseudo");


include '../inc/header.inc.php'; // début des affichages dans la page !
include '../inc/nav.inc.php';
?>
<main class="container">
  <div class="bg-light p-5 rounded">
    <h1><i class="fas fa-ghost"></i> Gestion des membres <i class="fas fa-ghost"></i></h1>
    <p class="lead">Bienvenue sur notre site.
      <hr><?php echo $msg; ?>
    </p>
  </div>

  <div class="row">
    <div class="col-12 mt-5">
      <div class="alert alert-info m-3">Vous pouvez SUPPRIMER un utilisateur mais vous ne pouvez <sapn class= alert-danger>MODIFIER QUE SON STATUT</span></div>
      <table class="table table-bordered text-center" id="myTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Pseudo</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Civilité</th>
            <th>Statut</th>
            <th>Date enregistrement</th>
            <th>Supprimer</th>
          </tr>
        </thead>
        <tbody>
          <?php
          while ($membre = $liste_membre->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            $a = $membre['id_membre'];
            foreach ($membre as $indice => $valeur) {
              if($indice == 'statut'){
                echo '<td contenteditable="true" data-id="'.$a.'" data-valeur="'.$indice.'" id="statut">' . $valeur . '</td>';
              }else{
                echo '<td>' . $valeur . '</td>';
              }
              
            }

            // Ajout d'un lien pour supprimer
            
            echo '<td><a href="?action=supprimer&id_membre=' . $membre['id_membre'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes vous sùr ?\'))"><i class="fas fa-dumpster-fire"></i></a></td>';
            echo '</tr>';
          }



          ?>
        </tbody>

      </table>

    </div>
  </div>
  
  </div>
</main>


   
<?php
include '../inc/footer.inc.php';
