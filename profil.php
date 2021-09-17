<?php
include 'inc/init.inc.php';
include 'inc/function.inc.php';
//restriction d'acces, si l'utilisateur n'est pas connecté, on redirige vers connexion.php
if (user_is_connected() == false) {
    header('Location:connexion.php');
}
$pseudo = '';
$mdp = '';
$nom = '';
$prenom = '';
$telephone = '';
$email = '';
$civilite = '';
$date_enregistrement = '';



if (
    isset($_POST['encienMdp']) &&
    isset($_POST['mdp']) &&
    isset($_POST['nom']) &&
    isset($_POST['prenom']) &&
    isset($_POST['telephone']) &&
    isset($_POST['email']) &&
    isset($_POST['civilite'])
) {


    $mdp = trim($_POST['mdp']);
    $encienMdp = trim($_POST['encienMdp']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $email = trim($_POST['email']);
    $civilite = trim($_POST['civilite']);

    // création des variables
    // création d'une variable controle qui nous permettre de savoir s'il y a eu des erreurs dans nos test
    $erreur = false;




    // controles:

    // controle sur le format du mail
    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        // cas erreur
        $erreur = true;
        $msg .= '<div class="alert alert-danger mb-3">votre email n\'est pas valide</div>';
    }

    //  - le mdp ne doit pas être vide 

    if (empty($mdp)) {
        $erreur = true;
        $msg .= '<div class="alert alert-danger mb-3">vous devez saisir un mot de passe</div>';
    }

    if (empty($nom)) {
        $erreur = true;
        $msg .= '<div class="alert alert-danger mb-3">vous devez saisir votre nom</div>';
    }

    if (empty($prenom)) {
        $erreur = true;
        $msg .= '<div class="alert alert-danger mb-3">vous devez saisir votre prenom</div>';
    }



    if ($erreur == false) {




        //requete pour pouvoir verifier le mot de passe de l'utilisateur avant de le laisser modifier ses infos de profil
        $req_mdp = $pdo->prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
        $req_mdp->bindParam(':id_membre', $_SESSION['membre']['id_membre'], PDO::PARAM_STR);
        $req_mdp->execute();
        $encienMdp = $req_mdp->fetch(PDO::FETCH_ASSOC);

        if (password_verify($mdp, $encienMdp['mdp'])) {
            // on lance le UPDATE:
            $enregistrement = $pdo->prepare("UPDATE membre SET mdp = :mdp, nom = :nom, prenom = :prenom, telephone = :telephone, email = :email, civilite = :civilite WHERE id_membre = :id_membre");
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
            $enregistrement->bindParam(':id_membre', $_SESSION['membre']['id_membre'], PDO::PARAM_STR);
            $enregistrement->bindParam(':mdp', $mdp, PDO::PARAM_STR);
            $enregistrement->bindParam(':nom', $nom, PDO::PARAM_STR);
            $enregistrement->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $enregistrement->bindParam(':telephone', $telephone, PDO::PARAM_STR);
            $enregistrement->bindParam(':email', $email, PDO::PARAM_STR);
            $enregistrement->bindParam(':civilite', $civilite, PDO::PARAM_STR);
            $enregistrement->execute();
            session_destroy();
            header('Location:connexion.php');
        } else {
            $msg .= '<div class="alert alert-danger mb-3">Erreur de saisie de votre mot de passe actuel</div>';
        }
    }
}

// recuperation de toutes les annonces de l'utilisateur connecté
$liste_annonces = $pdo->query("SELECT * FROM annonce WHERE membre_id = " . $_SESSION['membre']['id_membre'] . " ORDER BY date_enregistrement");

// Recuperation des commentaires de l'utilisateur
$liste_commentaires = $pdo->prepare("SELECT *, commentaire.date_enregistrement AS date, annonce.titre AS tire_a, membre.pseudo AS pseudo FROM commentaire AS commentaire INNER JOIN annonce AS annonce ON commentaire.annonce_id = annonce.id_annonce  INNER JOIN membre AS membre ON commentaire.membre_id = membre.id_membre WHERE commentaire.membre_id2 = " . $_SESSION['membre']['id_membre'] . "");
$liste_commentaires->execute();

// erengistrement de la réponse
if(isset($_POST['rep'])){
    $req_rep= $pdo->prepare("UPDATE commentaire SET reponses = :reponses WHERE id_commentaire = :id_commentaire");
    $req_rep->bindParam(':id_commentaire', $_POST['id_comm'], PDO::PARAM_STR);
    $req_rep->bindParam(':reponses', $_POST['rep'], PDO::PARAM_STR);
    $req_rep->execute();
    header('Location:profil.php');
}

// Récuperation des avis et calcul de la moyenne de la note du vendeur
$note = $pdo->query("SELECT avis, FLOOR(AVG(note)) FROM note WHERE membre_id2 = " . $_SESSION['membre']['id_membre'] . "");

include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>



<main class="container">
    <div class="bg-light p-5 rounded">
        <h1><i class="fas fa-ghost"></i> Template <i class="fas fa-ghost"></i></h1>
        <p class="lead">Bienvenue sur ma boutique.
            <hr><?php echo $msg; ?></hr>
            
        </p>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-6">
                        <ul class="list-group">
                            <li class="list-group-item"> <?php if ($_SESSION['membre']['civilite'] == 'f') {
                                                                echo 'Bonjour Madame ' . $_SESSION['membre']['nom'];
                                                            } else {
                                                                echo 'Bonjour monsieur ' . $_SESSION['membre']['nom'];
                                                            } ?></li>
                            <li class="list-group-item">Pseudo : <?php echo $_SESSION['membre']['pseudo'] ?></li>
                            <li class="list-group-item">Nom : <?php echo $_SESSION['membre']['nom'] ?></li>
                            <li class="list-group-item">Prénom : <?php echo $_SESSION['membre']['prenom'] ?></li>
                            <li class="list-group-item">Email : <?php echo $_SESSION['membre']['email'] ?></li>
                            <li class="list-group-item">Téléphone : <?php echo $_SESSION['membre']['telephone'] ?></li>
                            <li class="list-group-item">Statut : <?php if ($_SESSION['membre']['statut'] == 2) {
                                                                        echo 'vous etes administrateur du site';
                                                                    } else {
                                                                        echo 'vous etes un simple utilisateur du site';
                                                                    }  ?></li>
                            <li class="list-group-item">Votre moyenne de note : <?php 
                                                                            $moyenneNote = $note->fetch(PDO::FETCH_ASSOC);
                                                                            
                                                                            $moyenne = $moyenneNote['FLOOR(AVG(note))'];
                                                                            switch($moyenne){
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
                                                                             ?></li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <form method="get">
                            <a href="?action=modifier"><i class="fas fa-user-edit fa-2x" id="editProfil"> Modifier votre profil</i></a>
                        </form>
                        <?php
                        if (isset($_GET['action']) && $_GET['action'] == 'modifier') {
                            echo '<div class="alert alert-info">Votre Pseudo n\'est pas modifiable</div>';
                            echo '<form class="row border p-3" method="post">
                        <div class="col-sm-6 offset-sm-3">
                            <div class="mb-3">
                                <label for="encienMdp" class="form-label">Mot de passe actuel<i class="fas fa-user-alt"></i></label>
                                <input type="password" class="form-control" id="encienMdp" name="encienMdp">
                            </div>
                            <div class="mb-3">
                                <label for="mdp" class="form-label">Nouveau mot de passe <i class="fas fa-user-alt"></i></label>
                                <input type="password" class="form-control" id="mdp" name="mdp">
                            </div>
                            <div class="mb-3">
                                <label for="civilite" class="form-label">Civilite <i class="fas fa-user-alt"></i></label>
                                <select class="form-control" id="civilite" name="civilite">';
                            if ($_SESSION['membre']['civilite'] == 'm') {
                                echo '<option value="m" selected>homme</option>
                                    <option value="f">femme</option>
                                </select>';
                            } else {
                                echo '<option value="m">homme</option>
                                    <option value="f" selected>femme</option>
                                </select>';
                            }

                            echo '</div>
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom <i class="fas fa-user-alt"></i></label>
                                <input type="text" class="form-control" id="nom" name="nom" value="' . $_SESSION['membre']['nom'] . '">
                            </div>
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom <i class="fas fa-user-alt"></i></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="' . $_SESSION['membre']['prenom'] . '">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <i class="fas fa-user-alt"></i></label>
                                <input type="text" class="form-control" id="email" name="email" value="' . $_SESSION['membre']['email'] . '">
                            </div>
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone <i class="fas fa-user-alt"></i></label>
                                <input type="text" class="form-control" id="telephone" name="telephone" value="' . $_SESSION['membre']['telephone'] . '">
                            </div>
                            <div class="mb-3 mt-4">
                                <button type="submit" class="btn btn-outline-secondary w-100" id="inscription" ><i class="fas fa-keyboard"></i> Modifier <i class="fas fa-keyboard"></i></button>
                            </div>                            
                        </div>
                       
                    </form>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-5 border p-3">
                <table class="table table-bordered text-center" id="tableUserAnnonce">
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

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($annonce = $liste_annonces->fetch(PDO::FETCH_ASSOC)) {
                            echo '<tr>';

                            foreach ($annonce as $indice => $valeur) {
                                if ($indice == 'photo') {
                                    echo '<td><img src="' . URL . 'assets/images/' . $valeur . '" width="70" class="img-fluid" alt="image produit"></td>';
                                } elseif ($indice == 'description_longue') {
                                    echo '<td>' . substr($valeur, 0, 50) . ' <a href="#">...</a></td>';
                                } elseif ($indice == 'id_annonce') {
                                    echo '';
                                } elseif ($indice == 'photo_id') {
                                    echo '';
                                } elseif ($indice == 'membre_id') {
                                    echo '';
                                } elseif ($indice == 'categorie_id') {
                                    $req_categorie = $pdo->query("SELECT * FROM categorie WHERE id_categorie =" . $valeur . "");
                                    $categorie2 = $req_categorie->fetch(PDO::FETCH_ASSOC);
                                    echo '<td>' . $categorie2['titre'] . '</td>';
                                } else {
                                    echo '<td>' . $valeur . '</td>';
                                }
                            }
                        }



                        ?>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <?php
                while ($commentaire = $liste_commentaires->fetch(PDO::FETCH_ASSOC)) {
                    //var_dump($commentaire['reponses']);
                    if ($commentaire['reponses'] == "") {
                        echo '<div class="card">
                            <div class="card-header">
                              Commentaires en attente de réponses
                            </div>
                            <div class="card-body">
                              <h5 class="card-title">Question posée pour l\'annonce "' .  $commentaire['titre'] . '" le ' . $commentaire['date'] . ' par '.$commentaire['pseudo'].'</h5>
                              <p class="card-text">' .
                            $commentaire['commentaire'] . '</p><br>
                                
                                <div class="form-floating">
                                <form method="post">
                                    <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px" name="rep"></textarea>
                                    <input type="hidden" name="id_comm"/ value="'.$commentaire['id_commentaire'].'">
                                    <button type="submit" class="btn btn-primary">Répondre</button>
                                </form>
                                </div>
                            </div>
                          </div>';
                    }
                }
                ?>
            </div>
        </div>

    </div>
</main>

<?php
include 'inc/footer.inc.php';


