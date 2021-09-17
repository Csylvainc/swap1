<?php

// Fonction pour tester si l'utilisateur est connecté
function user_is_connected() {
    // on vérifie si SESSION n'existe pas ou est vide.
    if(empty($_SESSION['membre'])) {
        return false;
    } else {
        return true;
    }

}


// Fonction pour tester si l'utilisateur est admin
function user_is_admin() {
    if(user_is_connected() && $_SESSION['membre']['statut'] == 2) {
        return true;
    }
    return false; // après un return, on sort immédiatement de la fonction donc cette ligne ne sera pas lue si on est rentré dans le if. Comportement similaire si on met un else
}
// fonction pour enregistrer les photos
function add_photo($pdo,$msg, $id_annonce, $photo,$photo2,$photo3,$photo4,$photo5){
    global  $photoAnnonce;

    
    if (empty($_FILES['photo']['name']) && $photo=='') {
        return $msg.='merci de mettre une photo';
    }
    $enregistrementPhoto = $pdo->prepare("INSERT INTO photo (photo, photo2, photo3, photo4, photo5) VALUES (:photo, :photo2, :photo3, :photo4, :photo5)");
    
    foreach ($_FILES AS $files => $value){
   
      if($value['name']){
        
        if(!exif_imagetype($value['tmp_name'])){
           return $msg.='<div class="alert alert-danger mb-3">Attention mauvais format de fichier (unquement jpg, jpeg, gik, png ou webp) !</div>';
         }

        $$files=uniqid().'_' .$value['name'];
        $$files=str_replace(' ', '-', $$files);
        $$files=preg_replace('#[^A-Za-z0-9.\-]#', '', $$files);
        
        copy($value['tmp_name'], ROOT_PATH . PROJECT_PATH . 'assets/images/' . $$files);
        
       
      }
      $enregistrementPhoto->bindValue(':'.$files, isset($$files)?$$files:'');
    }
    $photoAnnonce = $photo;
    $enregistrementPhoto->execute();
    $photoId=$pdo->lastInsertId();
    return $photoId;
    
      
      
      
      
   
    }
    
   

