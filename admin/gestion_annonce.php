<?php
include '../inc/init.inc.php';
include '../inc/function.inc.php';
if (user_is_admin() == false) {
  header('Location:' . URL . 'connexion.php');
}


//----------------------------------------------------------------
//----------------------------------------------------------------
// Suppression d'un annonce
//----------------------------------------------------------------
//----------------------------------------------------------------

if(isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_annonce'])){
  // requete delete basée sur l'id_annonce pour supprimer l'annonce en question pour
  $reqAnnonceASupprimer = $pdo->prepare("SELECT * FROM annonce WHERE id_annonce = :id_annonce");
  $reqAnnonceASupprimer->bindParam(':id_annonce', $_GET['id_annonce'], PDO::PARAM_STR);
  $reqAnnonceASupprimer->execute();
  $annonceInfos = $reqAnnonceASupprimer->fetch(PDO::FETCH_ASSOC);
  $idPhoto = $annonceInfos['photo_id'];
  $supp = $pdo->prepare("DELETE FROM annonce WHERE id_annonce = :id_annonce");
  $supp->bindParam(':id_annonce', $_GET['id_annonce'], PDO::PARAM_STR);
  


  $suppPhoto = $pdo->prepare("DELETE FROM photo WHERE id_photo = :id_photo");
  $suppPhoto->bindParam(':id_photo', $idPhoto, PDO::PARAM_STR);

  $suppPhoto->execute();
  $supp->execute();

}
  

//----------------------------------------------------------------
//----------------------------------------------------------------
// Fin suppression d'un annonce
//----------------------------------------------------------------
//----------------------------------------------------------------

$id_annonce = '';
$titre = '';
$categorie = '';
$description_courte = '';
$description_longue = '';
$prix = '';
$photo = '';
$region = '';
$ville = '';
$adresse = '';
$cp = '';
$membre_id ='';
$photo_id ='';
$categorie_id ='';
$date_enregistrement ='';
$photo2="";
$photo3="";
$photo4="";
$photo5="";


//----------------------------------------------------------------
//----------------------------------------------------------------
// Modification d'une annonce
//----------------------------------------------------------------
//----------------------------------------------------------------

if(isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_annonce'])){
 // Pour la modification d'un annonce il faut proposer à l'utilisateur les données déjà enregistrées afin qu'il ne change que la ou les valeurs qu'il souhaite
 // Une requete pour récupérer les infos de cette annonce, un fetch et on affiche dasn le form via les variables déjà en place dans le form
 
 $modification = $pdo->prepare('SELECT * FROM annonce WHERE id_annonce = :id_annonce');
 $modification->bindParam(':id_annonce', $_GET['id_annonce'], PDO::PARAM_STR);
 $modification->execute();
 $infos_annonce = $modification->fetch(PDO::FETCH_ASSOC);
 $id_annonce = $infos_annonce['id_annonce'];
 $titre = $infos_annonce['titre'];
 $description_courte = $infos_annonce['description_courte'];
 $description_longue = $infos_annonce['description_longue'];
 $prix = $infos_annonce['prix'];
 $photo = $infos_annonce['photo'];
 $region = $infos_annonce['region'];
 $ville = $infos_annonce['ville'];
 $adresse = $infos_annonce['adresse'];
 $cp = $infos_annonce['cp'];
 $membre = $infos_annonce['membre_id'];
 $categorieM = $infos_annonce['categorie_id'];
 
 $req_liste_photo = $pdo->prepare("SELECT * FROM photo WHERE id_photo = :id_photo");
  $req_liste_photo->bindParam(':id_photo',$infos_annonce['photo_id'], PDO::PARAM_STR);
  $req_liste_photo->execute();
  $liste_photo = $req_liste_photo->fetch(PDO::FETCH_ASSOC);
  $photo2 = $liste_photo['photo2'];
  $photo3 = $liste_photo['photo3'];
  $photo4 = $liste_photo['photo4'];
  $photo5 = $liste_photo['photo5'];
  
}


//----------------------------------------------------------------
//----------------------------------------------------------------
// Fin modification d'un annonce
//----------------------------------------------------------------
//----------------------------------------------------------------


//----------------------------------------------------------------
//----------------------------------------------------------------
// ENREGISTREMENT ET MODIFICATION DES annonces
//----------------------------------------------------------------
//----------------------------------------------------------------
// isset de tous les champs sauf celui de la photo que nous allons traiter independamment

if (
 
 isset($_POST['categorie']) &&
 isset($_POST['titre']) &&
 isset($_POST['description_courte']) &&
 isset($_POST['description_longue']) &&
 isset($_POST['prix']) &&
 isset($_POST['region']) &&
 isset($_POST['ville']) &&
 isset($_POST['adresse']) &&
 isset($_POST['cp']) &&
 isset($_SESSION['membre']['id_membre'])
) {

 
 $categorie = trim($_POST['categorie']);
 $titre = trim($_POST['titre']);
 $description_courte = trim($_POST['description_courte']);
 $description_longue = trim($_POST['description_longue']);
 $prix = trim($_POST['prix']);
 $photo = '';
 $region = trim($_POST['region']);
 $ville = trim($_POST['ville']);
 $adresse = trim($_POST['adresse']);
 $cp = trim($_POST['cp']);
 $membre = trim($_SESSION['membre']['id_membre']);
 
 



 $erreur = false;
 // Modification
 // on verifie si l'id_annonce existe est n'est pas vide si true on est en modification
 if(!empty($_POST['id_annonce'])){
   $id_annonce  = trim($_POST['id_annonce']);
 }

 // Pour la modif de la photo
 if(!empty($_POST['ancienne_photo'])){
   $photo = $_POST['ancienne_photo'];
 }

 // Controle sur le titre de l'annonce (non vide et pas en double)
 if (empty($titre) && strlen($_POST['tritre'])<5) {
   $erreur = true;
   $msg .= '<div class="alert alert-danger mb-3">Attention le titre est obligatoire!</div>';
 }

 $verif_annonce = $pdo->prepare("SELECT * FROM annonce Where id_annonce = :id_annonce");
 $verif_annonce->bindParam(':id_annonce', $id_annonce, PDO::PARAM_STR);
 $verif_annonce->execute();

 // il ne faut pas verifier si la reference existe dans le cadre d'une modif donc on ajoute un controle sur id_annonce vide ou non
 if ($verif_annonce->rowCount() > 0 && empty($id_annonce)) {
   $erreur = true;
   $msg .= '<div class="alert alert-danger mb-3">Attention cette annonce est deja présente!</div>';
 }

 $return = add_photo($pdo,$msg, $id_annonce, $photo,$photo2,$photo3,$photo4,$photo5);
 
if(is_numeric($return)){
  if(empty($id_annonce)){
      
    $enregistrementAnnonce = $pdo->prepare("INSERT INTO annonce (titre, description_courte, description_longue, prix, photo, region, ville, adresse, cp, membre_id, photo_id, categorie_id) VALUES (:titre, :description_courte, :description_longue, :prix, :photo, :region, :ville, :adresse, :cp, :membre_id, :photo_id,:categorie_id)");

   
    $enregistrementAnnonce->bindParam(':titre', $titre, PDO::PARAM_STR);
    $enregistrementAnnonce->bindParam(':description_courte', $description_courte, PDO::PARAM_STR);
    $enregistrementAnnonce->bindParam(':description_longue', $description_longue, PDO::PARAM_STR);
    $enregistrementAnnonce->bindParam(':prix', $prix, PDO::PARAM_STR);
    $enregistrementAnnonce->bindParam(':photo', $photoAnnonce, PDO::PARAM_STR);
    $enregistrementAnnonce->bindParam(':region', $region, PDO::PARAM_STR);
    $enregistrementAnnonce->bindParam(':ville', $ville, PDO::PARAM_STR);
    $enregistrementAnnonce->bindParam(':adresse', $adresse, PDO::PARAM_STR);
    $enregistrementAnnonce->bindParam(':cp', $cp, PDO::PARAM_STR);
    $enregistrementAnnonce->bindParam(':membre_id', $membre, PDO::PARAM_STR);
    $enregistrementAnnonce->bindParam(':photo_id', $return, PDO::PARAM_STR);
    $enregistrementAnnonce->bindParam(':categorie_id', $categorie, PDO::PARAM_STR);

    $enregistrementAnnonce->execute();
    $msg.='<div class="alert alert-succes">enregistrement reussi</div>';
  }else{
      // Si $id_annonce n'est pas vide : UPDATE
     
      $modificationAnnonce = $pdo->prepare("UPDATE annonce SET titre = :titre, description_courte = :description_courte, description_longue = :description_longue, prix = :prix, photo = :photo, region = :region, ville = :ville, adresse= :adresse, cp = :cp, membre_id = :membre_id, categorie_id = :categorie_id, photo_id = :photo_id WHERE id_annonce = :id_annonce");

      $modificationAnnonce->bindParam(':titre', $titre, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':description_courte', $description_courte, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':description_longue', $description_longue, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':prix', $prix, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':photo', $photoAnnonce, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':region', $region, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':ville', $ville, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':adresse', $adresse, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':cp', $cp, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':membre_id', $membre, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':categorie_id', $categorie, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':photo_id', $return, PDO::PARAM_STR);
      $modificationAnnonce->bindParam(':id_annonce', $id_annonce, PDO::PARAM_STR);

      $modificationAnnonce->execute();
  }
}
}
 // Enregistrement en BDD
// if ($return !== true) {

  
//----------------------------------------------------------------
//----------------------------------------------------------------
//  FIN ENREGISTREMENT ET MODIFICATION DES liste_annonces
//----------------------------------------------------------------
//----------------------------------------------------------------


//----------------------------------------------------------------
//----------------------------------------------------------------
// LECTURE DE LA TABLE annonce
//----------------------------------------------------------------
//----------------------------------------------------------------

// recuperation de toutes les annonces 
$liste_annonces = $pdo->query("SELECT * FROM annonce ORDER BY date_enregistrement");


include '../inc/header.inc.php'; // début des affichages dans la page !
include '../inc/nav.inc.php';
?>
<main class="container">
 <div class="bg-light p-5 rounded">
   <h1>Gestion des annonces</h1>
   <p class="lead">Créez ou modifiez les annonces de tous les utilisateurs<hr><?php echo $msg; ?>
   </p>
 </div>

 <div class="row">
   <div class="col-12 mt-5">
     <!-- ne pas oublier l'attribut enctype="multipart/form-data", les pièces jointent seront dans une nouvelle super global : $_FILES -->
     <form class="row border p-3" method="post" enctype="multipart/form-data">
       <div class="col-sm-6">
         <!-- Ajout d'un champs cacher de l'id_annonce pour la modification -->
       <input type="hidden" readonly id="id_annonce" name="id_annonce" value="<?php echo $id_annonce; ?>">
       <!-- Ajout d'un champs cacher de l'ancienne photo pour la modification -->
       <input type="hidden" readonly id="ancienne_photo" name="ancienne_photo" value="<?php echo $photo; ?>">
       <input type="hidden" readonly id="ancienne_photo2" name="ancienne_photo2" value="<?php echo $photo2; ?>">
        <input type="hidden" readonly id="ancienne_photo3" name="ancienne_photo3" value="<?php echo $photo3; ?>">
        <input type="hidden" readonly id="ancienne_photo4" name="ancienne_photo4" value="<?php echo $photo4; ?>">
        <input type="hidden" readonly id="ancienne_photo5" name="ancienne_photo5" value="<?php echo $photo5; ?>">
         <div class="mb-3">
           <label for="titre" class="form-label">Titre <i class="text-primary fas fa-user-alt"></i></label>
           <input type="text" class="form-control" id="titre" name="titre" value="<?php echo $titre; ?>">
         </div>
         <div class="mb-3">
           <label for="categorie" class="form-label">Catégorie <i class="text-primary fas fa-user-alt"></i></label>
           <?php $liste_categorie = $pdo->query("SELECT * FROM categorie");?>
           <select class="form-control" id="categorie" name="categorie">
           <?php while ($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC)) { 
            if($categorieM==$categorie['id_categorie']){
              echo '<option value="' . $categorie['id_categorie'] .'" selected>' . $categorie['titre'] .'</option>';
            }else{
              echo '<option value="' . $categorie['id_categorie'] .'">' . $categorie['titre'] .'</option>';
            }
              
            }
            
            ?>
           </select>
           
           
         </div>
         <div class="mb-3">
           <label for="description_courte" class="form-label">Description courte <i class="text-primary fas fa-user-alt"></i></label>
           <input type="text" class="form-control" id="description_courte" name="description_courte" value="<?php echo $description_courte; ?>">
         </div>
         <div class="mb-3">
           <label for="description_longue" class="form-label">Description longue<i class="text-primary fas fa-user-alt"></i></label>
           <textarea class="form-control" id="description" name="description_longue" rows="4"><?php echo $description_longue; ?></textarea>
         </div>
         <div class="mb-3">
           <label for="prix" class="form-label">Prix<i class="text-primary fas fa-user-alt"></i></label>
           <input type="text" class="form-control" id="prix" name="prix" rows="4" value="<?php echo $prix; ?>">
         </div>
       </div>
       <div class="col-sm-6">
         <?php if(!empty($photo)) { ?>
           <div class="mb-3">
           <label for="photo" class="form-label">Photo actuelle <i class="text-primary fas fa-user-alt"></i></label>
           <img src="<?php echo URL . 'assets/images/' . $photo; ?>" alt="img_produit" width="140px">
           </div>
           <div class="mb-3">
            <label for="photo" class="form-label">Photo 2 actuelle <i class="text-primary fas fa-user-alt"></i></label>
            <img src="<?php echo URL . 'assets/images/' . $photo2; ?>" alt="img_produit" width="140px">
            </div>
            <div class="mb-3">
            <label for="photo" class="form-label">Photo 3 actuelle <i class="text-primary fas fa-user-alt"></i></label>
            <img src="<?php echo URL . 'assets/images/' . $photo3; ?>" alt="img_produit" width="140px">
            </div>
            <div class="mb-3">
            <label for="photo" class="form-label">Photo 4 actuelle <i class="text-primary fas fa-user-alt"></i></label>
            <img src="<?php echo URL . 'assets/images/' . $photo4; ?>" alt="img_produit" width="140px">
            </div>
            <div class="mb-3">
            <label for="photo" class="form-label">Photo 5 actuelle <i class="text-primary fas fa-user-alt"></i></label>
            <img src="<?php echo URL . 'assets/images/' . $photo5; ?>" alt="img_produit" width="140px">
            </div>
           <?php } ?>
         <div class="mb-3">
           <label for="photo" class="form-label">Photo <i class="text-primary fas fa-user-alt"></i></label>
           <input type="file" class="form-control" id="photo" name="photo">
           <label for="photo2" class="form-label">Photo 2<i class="text-primary fas fa-user-alt"></i></label>
           <input type="file" class="form-control" id="photo2" name="photo2">
           <label for="photo3" class="form-label">Photo 3<i class="text-primary fas fa-user-alt"></i></label>
           <input type="file" class="form-control" id="photo3" name="photo3">
           <label for="photo4" class="form-label">Photo 4<i class="text-primary fas fa-user-alt"></i></label>
           <input type="file" class="form-control" id="photo4" name="photo4">
           <label for="photo5" class="form-label">Photo 5<i class="text-primary fas fa-user-alt"></i></label>
           <input type="file" class="form-control" id="photo5" name="photo5">
         </div>
         <div class="mb-3">
           <label for="region" class="form-label">Région <i class="text-primary fas fa-user-alt"></i></label>
           <input type="text" class="form-control" id="region" name="region" value="<?php echo $region; ?>">
           <div style="display: none; color:#f55;" id="errorMessage"></div>
         </div>
         <div class="mb-3">
           <label for="cp" class="form-label">Code postal <i class="text-primary fas fa-user-alt"></i></label>
           <input type="text" class="form-control" id="cp" name="cp" value="<?php echo $cp; ?>">
           <div style="display: none; color:#f55;" id="errorMessage"></div>
         </div>
         <div class="mb-3">
           <label for="ville" class="form-label">Ville <i class="text-primary fas fa-user-alt"></i></label>
           <input type="text" class="form-control" id="ville" name="ville" value="<?php echo $ville; ?>">
         </div>
         <div class="mb-3">
           <label for="adresse" class="form-label">Adresse<i class="text-primary fas fa-user-alt"></i></label>
           <input type="text" class="form-control" id="adresse" name="adresse" value="<?php echo $adresse; ?>">
           <div style="display: none; color:#f55;" id="errorMessage"></div>
         </div>
         <div class="mb-3 mt-4">
           <button type="submit" class="btn btn-outline-primary w-100 mt-2" id="enregistrement"><i class="fas fa-keyboard"></i> Enregistrement <i class="fas fa-keyboard"></i></button>
         </div>
       </div>
     </form>
   </div>
   <div class="col-12 mt-5 border p-3">
     <table class="table table-bordered text-center" id="tableAdminAnnonce">
       <thead>
         <tr>
           
           <th>Titre</th>
           <th>description courte</th>
           <th>description longue</th>
           <th>prix</th>
           <th>photo</th>
           <th>region</th>
           <th>ville</th>
           <th>adresse</th>
           <th>cp</th>
           <th>categorie</th>
           <th>date d'enregistrement</th>
           <th>Modifier</th>
           <th>Supprimer</th>
         </tr>
       </thead>
       <tbody>
         <?php
           while($annonce = $liste_annonces->fetch(PDO::FETCH_ASSOC)){
               echo '<tr>';

               foreach($annonce AS $indice => $valeur){
                   if($indice == 'photo'){
                     echo '<td><img src="' . URL . 'assets/images/' .$valeur . '" width="70" class="img-fluid" alt="image produit"></td>';
                   }elseif($indice == 'description_longue'){
                     echo '<td>' .substr($valeur,0,50) . ' <a href="#">...</a></td>';
                   }elseif($indice == 'id_annonce'){
                     echo '';  
                   }elseif($indice == 'photo_id'){
                     echo '';  
                   }elseif($indice == 'membre_id'){
                     echo '';  
                   }
                   elseif($indice == 'categorie_id'){
                     $req_categorie = $pdo->query("SELECT * FROM categorie WHERE id_categorie =".$valeur."");
                     $categorie2 = $req_categorie->fetch(PDO::FETCH_ASSOC);
                     echo '<td>' .$categorie2['titre'] . '</td>';
                   }else{
                     echo '<td>' .$valeur . '</td>';
                   }
               }

               // Rajout de deux liens pour modif et supprime
               echo '<td><a href="?action=modifier&id_annonce=' . $annonce['id_annonce'] . '" class="btn btn-danger"><i class="fas fa-edit"></i></a></td>';
               echo '<td><a href="?action=supprimer&id_annonce=' . $annonce['id_annonce'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes vous sùr ?\'))"><i class="fas fa-dumpster-fire"></i></a></td>';
               echo '</tr>';
           }



         ?>
       </tbody>

     </table>
   </div>
 </div>
</main>


<?php
include '../inc/footer.inc.php';
