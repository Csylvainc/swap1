<?php 
include 'inc/init.inc.php';
include 'inc/function.inc.php';

 //Restriction d'accés, si l'utilisateur est connecter on le redirige vers profil.php
 if(user_is_connected()== true){
    header('Location:profil.php');
  }
// création des variables qui vont nous permettrent de re remplir le formulaire en cas d'erreur de saisi de l'utilisateur
$pseudo = '';
$mdp = '';
$nom = '';
$prenom = '';
$telephone = '';
$email = '';
$civilite = '';
$date_enregistrement = '';



if (isset($_POST['pseudo']) && 
    isset($_POST['mdp']) && 
    isset($_POST['nom']) && 
    isset($_POST['prenom']) && 
    isset($_POST['telephone']) && 
    isset($_POST['email']) && 
    isset($_POST['civilite'])
   ) {
  $pseudo = trim($_POST['pseudo']);
  $mdp = trim($_POST['mdp']);
  $nom = trim($_POST['nom']);
  $prenom = trim($_POST['prenom']);
  $telephone = trim($_POST['telephone']);
  $email = trim($_POST['email']);
  $civilite = trim($_POST['civilite']);

  // création des variables
  // création d'une variable controle qui nous permettre de savoir s'il y a eu des erreurs dans nos test
  $erreur = false;
  



  // controles:
  //  - taille du pseudo
  if(iconv_strlen($pseudo) < 4 || iconv_strlen($pseudo) > 14){
    // cas d'erreurs
    $erreur = true;
    $msg.= '<div class="alert alert-danger mb-3">Attention le pseudo doit avoir entre 4 et 14 caractère inclus!</div>';
  }
  //  - caractères présent dans le pseudo
      // nous devons utiliser une expression régulière
      // preg_match() est une fonction prédéfinie permettant de tester une chaine selon une regex et renvoie true si elle corespond.
      $verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#', $pseudo);
      if($verif_caractere == false){
        $erreur = true;
        $msg.= '<div class="alert alert-danger mb-3">Attention le pseudo ne doit pas contenir de caractères spéciaux !</div>';
      }
  //  - disponibilité du Pseudo
      $verif_pseudo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
      $verif_pseudo->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
      $verif_pseudo->execute();
      
      if($verif_pseudo->rowCount() !==0){
        $erreur = true;
        $msg.= '<div class="alert alert-danger mb-3">Ce pseudo est deja pris !</div>';
      }

  // controle sur le format du mail
  if( filter_var($email, FILTER_VALIDATE_EMAIL) == false ) {
    // cas erreur
    $erreur = true;
    $msg.='<div class="alert alert-danger mb-3">votre email n\'est pas valide</div>';
}
  
  //  - le mdp ne doit pas être vide 

  if(empty($mdp)){
    $erreur = true;
    $msg.='<div class="alert alert-danger mb-3">vous devez saisir un mot de passe</div>';
  }

  if($erreur == false){
    // cryptage du mdp par hachage
    $mdp = password_hash($mdp, PASSWORD_DEFAULT);

    // on lance le insert into:
    $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, telephone, email, civilite, statut, date_enregistrement) VALUES (:pseudo, :mdp, :nom, :prenom, :telephone, :email, :civilite, 1, NOW())");
    
    $enregistrement->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $enregistrement->bindParam(':mdp', $mdp, PDO::PARAM_STR);
    $enregistrement->bindParam(':nom', $nom, PDO::PARAM_STR);
    $enregistrement->bindParam(':prenom', $prenom, PDO::PARAM_STR);
    $enregistrement->bindParam(':telephone', $telephone, PDO::PARAM_STR);
    $enregistrement->bindParam(':email', $email, PDO::PARAM_STR);
    $enregistrement->bindParam(':civilite', $civilite, PDO::PARAM_STR);
    $enregistrement->execute();
    
    // Pour éviter de renvoyer le même enregistrement en rechargeant la page, on va rediriger avec PHP sur cette page après l'enregistrement pour perdre les données dans $_POST
     header('location:connexion.php');
  }

}else{
   $msg.='<div class="mt-5 alert alert-info">merci de remplir tous les champs du formulaire</div>';
}
  

  


include 'inc/header.inc.php'; // début des affichages dans la page !
include 'inc/nav.inc.php';
?>
        <main class="container">
          
            <div class="bg-light p-5 rounded">
                <h1><i class="fas fa-ghost"></i> Inscription <i class="fas fa-ghost"></i></h1>
                <p class="lead">Bienvenue sur SWAP.<hr></p>                
            </div>

            <div class="row">
                <div class="col-12 mt-5">
                <?php echo $msg; ?>
                    <form class="row border p-3" method="post">
                        <div class="col-sm-6 offset-sm-3">
                            <div class="mb-3">
                                <label for="pseudo" class="form-label">Pseudo <i class="fas fa-user-alt"></i></label>
                                <input type="text" class="form-control" id="pseudo" name="pseudo" value="<?php echo $pseudo ?>">
                            </div>
                            <div class="mb-3">
                                <label for="mdp" class="form-label">Mot de passe <i class="fas fa-user-alt"></i></label>
                                <input type="password" class="form-control" id="mdp" name="mdp">
                            </div>
                            <div class="mb-3">
                                <label for="civilite" class="form-label">Civilite <i class="fas fa-user-alt"></i></label>
                                <select class="form-control" id="civilite" name="civilite">
                                    <option value="m">homme</option>
                                    <option value="f" <?php echo ($civilite == 'f') ? 'selected' : '' ; ?> >femme</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom <i class="fas fa-user-alt"></i></label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nom ?>">
                            </div>
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom <i class="fas fa-user-alt"></i></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $prenom ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <i class="fas fa-user-alt"></i></label>
                                <input type="text" class="form-control" id="email" name="email" value="<?php echo $email?>">
                            </div>
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone <i class="fas fa-user-alt"></i></label>
                                <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $telephone?>">
                            </div>
                            <div class="mb-3 mt-4">
                                <button type="submit" class="btn btn-outline-secondary w-100" id="inscription" ><i class="fas fa-keyboard"></i> Inscription <i class="fas fa-keyboard"></i></button>
                            </div>                            
                        </div>
                       
                    </form>
                </div>                
            </div>
        </main>

<?php 
include 'inc/footer.inc.php';
 