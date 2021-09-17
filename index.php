<?php
include 'inc/init.inc.php';
include 'inc/function.inc.php';


//code php
// requete de recupération des catégories pour le filtre categorie
$reponse_categorie = $pdo->query('SELECT * FROM categorie GROUP BY id_categorie');
// requete de recupération des membres pour le filtre membre
$reponse_membre = $pdo->query('SELECT * FROM membre GROUP BY id_membre');
// requete de recupération des regions pour le filtre region
$reponse_region = $pdo->query('SELECT * FROM annonce GROUP BY region');






//requete auto construite pour afficher les annonces en prnant en compte les filtres


// construction des WHERE
$requeteFiltre = "";
if(isset($_GET['categorie']) && $_GET['categorie']!='Tous'){
  $requeteFiltre.= 'categorie_id = :categorie AND ';
}
if(isset($_GET['membre']) && $_GET['membre']!='Tous'){
  $requeteFiltre.= 'membre_id = :membre AND ';
}
if(isset($_GET['region']) && $_GET['region']!='Tous'){
  $requeteFiltre.= 'region = :region AND ';
}
if(isset($_GET['recherche'])){
  $requeteFiltre.= 'description_courte LIKE :description_courte AND ';
  $recherche= '%'.$_GET['recherche'].'%';
}

// WHERE de base
$requeteFiltre.='prix<=:prix ';
if(isset($_GET['b'])){
  $prix = $_GET['b'];
}else{
  $prix= 600000;
}



// Construction des ORDER BY
if(isset($_GET['ordre']) && ($_GET['ordre']=='date' || ($_GET['ordre']=='prix ASC' || $_GET['ordre']=='prix DESC'))){
    $requeteFiltre.= 'ORDER BY '.$_GET['ordre'];
}elseif(isset($_GET['ordre']) && $_GET['ordre']=='moyenne'){
  $requeteFiltre.= 'ORDER BY membre.'.$_GET['ordre'] . ' DESC';
}
else{
  $requeteFiltre.= 'ORDER BY annonce.date_enregistrement DESC';
}


$liste_annonces = $pdo->prepare("SELECT *, annonce.titre AS titre_a, categorie.titre AS titre_c FROM annonce AS annonce  INNER JOIN categorie AS categorie ON annonce.categorie_id = categorie.id_categorie INNER JOIN membre AS membre ON annonce.membre_id = membre.id_membre WHERE ".$requeteFiltre);

$liste_annonces->bindParam(':prix', $prix, PDO::PARAM_STR);



if(isset($_GET['categorie']) && $_GET['categorie']!='Tous'){$liste_annonces->bindParam(':categorie', $_GET['categorie'], PDO::PARAM_STR);}
if(isset($_GET['membre']) && $_GET['membre']!='Tous'){$liste_annonces->bindParam(':membre', $_GET['membre'], PDO::PARAM_STR);}
if(isset($_GET['region']) && $_GET['region']!='Tous'){$liste_annonces->bindParam(':region', $_GET['region'], PDO::PARAM_STR);}
if(isset($_GET['recherche'])){$liste_annonces->bindParam(':description_courte', $recherche, PDO::PARAM_STR);}

$liste_annonces->execute();



 
include 'inc/header.inc.php';
include 'inc/nav.inc.php';

?>



<main class="container">
  <div class="bg-light p-5 rounded">
    <h1><i class="fas fa-atom"></i> Site factice pour une vrai commande<a href="http://leboncoin.fr"> le bon coin </a><i class="fas fa-atom"></i></h1>
    <p class="lead">
      <hr><?php echo $msg; ?></hr>
    </p>
    <div class="row">
      <div class="col-3 mt-5">
        <?php

        // une requete qui recupère toutes les DIFFERENTES catégories pour les afficher ici en lien a href dans une liste ul li


        $liste_categorie = $reponse_categorie->fetchAll(PDO::FETCH_ASSOC);
        $liste_membre = $reponse_membre->fetchAll(PDO::FETCH_ASSOC);
        $liste_region = $reponse_region->fetchAll(PDO::FETCH_ASSOC);


        ?>
        <form method="get" class="form-row">
          <div>
          <label for="b">Prix</label>
          <input class="col-12" type="range" name="b" value="600000" min="0" max="600000" oninput="prixMax.value=parseInt(a.value)+parseInt(b.value)" />
          <input type="number" class="d-none" name="a" value="0" />
          <output name="prixMax">600000</output>
          </div>


          <div>
          <label for="ordre" class="col-12 mt-5">Trier par</label>
          <select class="col-12 my-2" name="ordre" id="prixSelect">
            <option value="date_enregistrement">Date</option>
            <option value="prix ASC">Prix croissant</option>
            <option value="prix DESC">Prix décroissant</option>
            <option value="moyenne">Les mieux notés</option>
          </select>
          <label class="col-12" for="categorie">Catégorie</label>
          <select name="categorie" id="categorie">
            <option value="Tous" selected>Tous les produits</option>
            <?php
            foreach ($liste_categorie as $sous_tableau) {
              echo '<option value="' . $sous_tableau['id_categorie'] . '">' . $sous_tableau['titre'] . '</option>';
              // var_dump($sous_tableau);
            }
            ?>
          </select>
          </div>
          <div>
          <label class="col-12 mt-5" for="membre">Membre</label>
          <select name="membre" id="membre_id">
            <option value="Tous">Tous les membres</option>
            <?php
            foreach ($liste_membre as $sous_tableau) {
              echo '<option value="' . $sous_tableau['id_membre'] . '">' . $sous_tableau['pseudo'] . '</option>';
              // var_dump($sous_tableau);
            }
            ?>
          </select>
          </div>
          <div>
          <label class="my-5" for="region">region</label>
          <select name="region" id="region">
            <option value="Tous">France entière</option>
            <?php
            foreach ($liste_region as $sous_tableau) {
              echo '<option value="' . $sous_tableau['region'] . '">' . $sous_tableau['region'] . '</option>';
              // var_dump($sous_tableau);
            }
            ?>
          </select>
          </div>
          <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>
      </div>
      <div class="col-9 mt-5">
        <div class="row">




          <?php
          if ($liste_annonces->rowCount() < 1) {
            echo '<div class="col-12 alert alert-danger">Aucun annonces ne correspond</di>';
          } else {
            while ($annonce = $liste_annonces->fetch(PDO::FETCH_ASSOC)) {
              //var_dump($annonce);
              $id = $annonce['membre_id'];
              $annonce_membre = $pdo->query('SELECT * FROM membre WHERE id_membre = '.$id.'');
              $membre = $annonce_membre->fetch(PDO::FETCH_ASSOC);
              $moyenne =$membre['moyenne'];
              echo '<div class="col-sm-4 p-3">
               <div class="card" style="width: 18rem;">
                 <img class=card-img-top src="' . URL . 'assets/images/' . $annonce['photo'] . '" height="250" class="img-fluid" alt="image produit">
                 <div class="card-body">
                   <a href="?categorie=' . $annonce['categorie_id'] . '"><h5 class="card-title">' . $annonce['titre'] . '</h5></a>
                   <p class="card-text">Postée par '. $membre['pseudo'] .'</p>
                   <p class="card-text"><br>'; switch($moyenne){
                    case 0:
                        echo '<span class="bg-info">'. $membre['pseudo'] .' n\'a pas encore de note</span><br>'; 
                        echo '<i class="far fa-star"></i>';
                        break;
                    case 1:
                        echo  '<i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
                        break;
                    case 2:
                        echo  '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
                        break;
                    case 3:
                        echo  '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
                        break;
                    case 4:
                        echo  '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>';
                        break;
                    case 5:
                        echo '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>';
                        break;
                } 
                   echo' </p>
                   <p class="card-text">' . $annonce['description_courte'] . '<br>Prix : ' . $annonce['prix'] . ' €<br>' . mb_strimwidth($annonce['description_longue'], 0, 25, " ...") . '</p>
                   <a href="fiche_annonce.php?id_annonce=' . $annonce['id_annonce'] . '" class="btn btn-primary">Détail produit</a>
                 </div>
               </div>
             </div>';
            }
          }


          ?>
        </div>
      </div>
    </div>

  </div>
</main>
<!-- <script src="<?php echo URL; ?>assets/js/filtre.js"></script> -->

<?php
include 'inc/footer.inc.php';
