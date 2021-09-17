<?php
include 'inc/init.inc.php';
include 'inc/function.inc.php';

$msg = "";

if (isset($_GET['id_annonce'])) {
    $annonceId = $_GET["id_annonce"];
    $reqAnnonce = $pdo->prepare("SELECT * FROM annonce WHERE id_annonce = :annonce");
    $reqAnnonce->bindParam(':annonce', $annonceId, PDO::PARAM_STR);
    $reqAnnonce->execute();
    $annonce = $reqAnnonce->fetch(PDO::FETCH_ASSOC);
    $idPhoto = $annonce['photo_id'];
    $liste_photo = $pdo->prepare("SELECT photo.photo, photo2, photo3, photo4, photo5 FROM photo INNER JOIN  annonce AS annonce ON photo.id_photo = annonce.photo_id WHERE photo.id_photo = :id");
    $liste_photo->bindParam(':id', $idPhoto, PDO::PARAM_STR);
    $liste_photo->execute();
    $photoAnnonce = $liste_photo->fetch(PDO::FETCH_ASSOC);
}

$id = $_GET['id_annonce'];
if ($_SESSION) {
    $idMembre = $_SESSION['membre']['id_membre'];
}

/////////////////////////// Enregistrement des commentaires ou/ et note //////////////////////////////////
// Commentaire
if (isset($_POST['commentaire']) && $_POST['commentaire'] != "") {
    $commentaire = $_POST['commentaire'];

    // ON COMMENCE L'ENREGISTREMENT
    $enregistre_comm = $pdo->prepare("INSERT INTO commentaire (membre_id, membre_id2, annonce_id, commentaire,date_enregistrement) VALUES (:membre_id, :membre_id2,:annonce_id, :commentaire, NOW())");
    $enregistre_comm->bindParam(':membre_id', $_SESSION['membre']['id_membre'], PDO::PARAM_STR);
    $enregistre_comm->bindParam(':membre_id2', $annonce['membre_id'], PDO::PARAM_STR);
    $enregistre_comm->bindParam(':annonce_id', $id, PDO::PARAM_STR);
    $enregistre_comm->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
    $enregistre_comm->execute();
    //header('location:index');

} elseif (isset($_POST['commentaire']) && $_POST['commentaire'] == "") {
    $msg .= 'pas de commentaire vide';
}

// Avis
if (isset($_POST['note']) && isset($_POST['avis']) && $_POST['note'] != 0) {
    $note = $_POST['note'];
    $avis = $_POST['avis'];



    // ON COMMENCE L'ENREGISTREMENT
    $enregistre_avis = $pdo->prepare("INSERT INTO note (membre_id1, membre_id2, note, avis, date_enregistrement) VALUES (:membre_id1, :membre_id2, :note, :avis, NOW())");
    $enregistre_avis->bindParam(':membre_id1', $_SESSION['membre']['id_membre'], PDO::PARAM_STR);
    $enregistre_avis->bindParam(':membre_id2', $annonce['membre_id'], PDO::PARAM_STR);
    $enregistre_avis->bindParam(':note', $note, PDO::PARAM_STR);
    $enregistre_avis->bindParam(':avis', $avis, PDO::PARAM_STR);

    $enregistre_avis->execute();

    // On UPDATE la note moyenne dans la table membre
    $note = $pdo->query("SELECT FLOOR(AVG(note)) FROM note WHERE membre_id2 = " . $annonce['membre_id'] . "");

    $moyenneNote = $note->fetch(PDO::FETCH_ASSOC);                            
    $moyenne = $moyenneNote['FLOOR(AVG(note))'];
    $mise_a_jour_Moyenne = $pdo->prepare("UPDATE membre SET moyenne=:moyenne WHERE id_membre=:id_membre");
    $mise_a_jour_Moyenne->bindParam(':moyenne',$moyenne, PDO::PARAM_STR);
    $mise_a_jour_Moyenne->bindParam(':id_membre',$annonce['membre_id'], PDO::PARAM_STR);
    $mise_a_jour_Moyenne->execute();
    //header('location:index');
    

}


// Recuperation des commentaire
$liste_commentaires = $pdo->prepare("SELECT * FROM commentaire WHERE annonce_id = $id ORDER BY date_enregistrement DESC");
$liste_commentaires->execute();

// On propose d'autres annonces de la categorie
$liste_annonces = $pdo->query("SELECT id_annonce, titre, description_courte, prix, photo FROM annonce WHERE categorie_id = ".$annonce['categorie_id']." ORDER BY date_enregistrement LIMIT 5");


include 'inc/header.inc.php';
include 'inc/nav.inc.php';

?>



<main class="container">
    <div class="bg-light p-5 rounded">
        <h1><i class="fas fa-atom"></i> Site factice pour une vrai commande<a href="http://leboncoin.fr"> le bon coin </a><i class="fas fa-atom"></i></h1>
        <p class="lead">
            <hr><?php echo $msg; ?></hr>

        </p>

        <div class="row justify-content-center">




            <?php


            echo ' <div class="card col-12 p-5" style="width: 50rem;">
                        <div class="card-body">
                            <h5 class="card-title">Titre : ' . $annonce['titre'] . '<br>Prix :  ' . $annonce['prix'] . ' €</h5>
                            <h6 class="card-subtitle mb-2 text-muted">' . $annonce['description_courte'] . '</h6>
                            <p class="card-text">' . $annonce['description_longue'] . '</p>';
                            
                            foreach($photoAnnonce AS $photo => $value){
                               
                                if($value!=''){
                                    echo '<button class="m-1 p-3" data-bs-toggle="modal" data-bs-target="#'.$photo.'"><img src="' . URL . 'assets/images/' . $value . '" alt="' . $annonce['titre'] . '" style="width:150px;"></button>';
                                    echo '<!-- Modal -->';
                                    echo '<div class="modal fade" id="'.$photo.'" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">';
                                     echo' <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                         
                                          <div class="modal-body">';
                                           echo '<img src="' . URL . 'assets/images/' . $value . '" alt="' . $annonce['titre'] . '" style="width:100%;">';
                                         echo' </div>
                                          
                                        </div>
                                      </div>
                                    </div>';
                                }
                                
                            }
                           echo'
                        </div>
                    </div>';




            ?>
            <div class="row justify-content-around">
                <div class="col-2">
                    <button type="button" class="btn btn-primary m-4" data-bs-toggle="modal" data-bs-target="#Modal1">
                        Laisser un commentaire
                    </button>
                    <!-- Modal commentaire -->

                    <div class="modal fade" id="Modal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="titre">Commentaire</h5>

                                </div>
                                <div class="modal-body">
                                    <?php if (!user_is_connected()) {

                                        $msg .= 'Vous devez être connecté pour laisser un commantaire';
                                        echo $msg;
                                    } elseif ($idMembre == $annonce['membre_id']) {
                                        $msg .= 'Vous ne pouvez pas noter ou commenter une de vos annonces';
                                        echo $msg;
                                    } else {
                                        echo '<div class="row">
                                                <div class="col-12 m-2 p-2">
                                                    <form method="post" id="comm">
                                                        <textarea class="form-control" placeholder="Laisser un commentaire" name="commentaire" id="commentaire"></textarea>
                                                        <button type="submit" class="btn btn-outline-secondary w-100" form="comm">Envoyer le commentaire</button>
                                                    </form>
                                                </div>
                                                
                                            </div>';
                                    } ?>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-primary m-4" data-bs-toggle="modal" data-bs-target="#Modal3">
                        Noter le vendeur
                    </button>
                    <!-- Modal note -->

                    <div class="modal fade" id="Modal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="titre">note</h5>

                                </div>
                                <div class="modal-body">
                                    <?php if (!user_is_connected()) {
                                        $msg="";
                                        $msg .= 'Vous devez être connecté pour laisser une note';
                                        echo $msg;
                                    } elseif ($idMembre == $annonce['membre_id']) {
                                        $msg .= 'Vous ne pouvez pas noter une de vos annonces';
                                        echo $msg;
                                    } else {
                                        echo '
                                        <div class="row">
                                        <div class="col-12 m-2 p-2">
                                                    <form method="post" id="noter">
                                                        <div class="stars text-center">
                                                            <i class="lar la-star" data-value="1"></i><i class="lar la-star" data-value="2"></i><i class="lar la-star" data-value="3"></i><i class="lar la-star" data-value="4"></i><i class="lar la-star" data-value="5"></i>
                                                        </div>
                                                        <input type="hidden" name="note" id="note" value="0">
                                                        <textarea class="form-control" placeholder="Laisser un avis" name="avis" requierd></textarea>
                                                        <button type="submit" class="btn btn-outline-secondary w-100" form="noter">Noter</button>
                                                        
                                                    </form>
                                                    </div>
                                                    </div>';
                                    } ?>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-primary m-4" data-bs-toggle="modal" data-bs-target="#Modal2">
                        Contacter le vendeur
                    </button>
                </div>
            </div>
            <!-- Modal contact -->
            <?php
            $reqmembre = $pdo->query("SELECT * FROM membre WHERE id_membre = " . $annonce['membre_id'] . "");
            $membreAnnonce = $reqmembre->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="modal fade" id="Modal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Contacter le vendeur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?php echo $membreAnnonce['telephone'] ?>
                        </div>
                        <form method="post">
                            <textarea class="form-control" placeholder="Votre message" id="floatingTextarea2" style="height: 100px"></textarea>
                            <div class="text-center">
                                <button type="submit" class="btn btn-secondary m-3">Enoyer</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>



            <div class="col-12 m-4 p-5">

                <iframe width="95%" height="250" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=AIzaSyCB3c5bwe1YQDtC0j32ppdwnkmofc8mZRU&q=<?php echo $annonce['adresse']; ?>,<?php echo $annonce['ville']; ?>+France" allowfullscreen>
                </iframe>
            </div>

            <!-- commentaires et réponses -->
            <div class="col-12">

                <?php
                $i = 0;
                while ($commentaire = $liste_commentaires->fetch(PDO::FETCH_ASSOC)) {
                    $rec_membre = $pdo->query('SELECT pseudo FROM membre WHERE id_membre = ' . $commentaire['membre_id'] . '');
                    $membre = $rec_membre->fetch(PDO::FETCH_ASSOC);
                    $i++;
                    echo '<div class="accordion accordion-flush" id="accordion' . $i . '">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse' . $i . '" aria-expanded="false" aria-controls="flush-collapse' . $i . '">
                                Question posée par ' .  $membre['pseudo'] . ' le ' . $commentaire['date_enregistrement'] . '<br> ' .
                        $commentaire['commentaire'] . '
                            </button>
                        </h2>';
                    if ($commentaire['reponses'] == "") {
                        echo '<div id="flush-collapse' . $i . '" class="accordion-collapse collapse" aria-labelledby="flush-collapse' . $i . '" data-bs-parent="#accordion' . $i . '">
                            
                            <div class="accordion-body">Pas encore de réponse.</div>
                            </div>
                            ';
                    } else {
                        echo '<div id="flush-collapse' . $i . '" class="accordion-collapse collapse" aria-labelledby="flush-collapse' . $i . '" data-bs-parent="#accordion' . $i . '">
                            
                                <div class="accordion-body">' . $commentaire['reponses'] . '</div>
                                </div>
                                ';;
                    };
                    '
                            
                        </div>
                    </div>

                    </div>';
                }

                ?>





            </div>
            <div class="col-12">
            <div class="p-3 m-4">
            <p>Annonces qui pourrez vouz plaire</p>
                <div class="row">
                    <?php

                    while ($annonce = $liste_annonces->fetch(PDO::FETCH_ASSOC)) {
                        echo  '<div class="col-2">
     <div class=" bg-light border-dark border">
     <img  src="' . URL . 'assets/images/' . $annonce['photo'] . '
    " class="card-img-top img-fluid" alt="photo_produit">
    <div class="card-body"><h4>' . $annonce['titre'] . '</h4>
   <button type="button" class="btn btn-outline-dark"><a href="fiche_annonce.php?id_annonce=' . $annonce['id_annonce'] . '
    "style="text-decoration: none; color: black;">Voir l\'annonce</a></button></div></div></div>';
                    }

                    ?>
                </div>
            </div>
           


            </div>
        </div>
    </div>

    </div>
</main>
<!-- <script src="<?php echo URL; ?>assets/js/filtre.js"></script> -->

<?php
include 'inc/footer.inc.php';
