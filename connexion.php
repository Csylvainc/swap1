<?php 
include 'inc/init.inc.php';
include 'inc/function.inc.php';

    //DECONNEXION UTILISATEUR
    if( isset($_GET['action']) && $_GET['action'] == 'deconnexion'){
        session_destroy();
    }

    //Restriction d'accés, si l'utilisateur est connecter on le redirige vers profil.php
    if(user_is_connected()== true){
        header('Location:profil.php');
      }

    $pseudo = '';

    // Si le formulaire a été validé
    if( isset($_POST['pseudo']) && isset($_POST['mdp']) ) {
        $pseudo = trim($_POST['pseudo']);
        $mdp = trim($_POST['mdp']);

        // on déclenche une requete selon le pseudo
        $connexion = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
        $connexion->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $connexion->execute();

        // on vérifie si on a une ligne. Si c'est le cas le pseudo est ok sinon erreur
        if( $connexion->rowCount() > 0 ) {
            // pseudo ok
            // on doit vérifier le mdp
            $infos = $connexion->fetch(PDO::FETCH_ASSOC); // 1 seule ligne = 1 fetch (pas de boucle)
            // echo '<pre>'; print_r($infos); echo '</pre>';
            // password_verify(mdp_du_form, mdp_de_la_bdd) permet de confirmer si le mdp correspond à celui enregistré au préalable
            if( password_verify($mdp, $infos['mdp']) ) {
                // mdp ok
                // Afin de conserver les informations de l'utilisateur, on place la réponse de la BDD dans la session.
                // Cela nous permettra ensuite en demandant si ces informations sont présentes si l'utilisateur est bien connecté
                $_SESSION['membre'] = array();
                $_SESSION['membre']['id_membre'] = $infos['id_membre'];
                $_SESSION['membre']['pseudo'] = $infos['pseudo'];
                $_SESSION['membre']['nom'] = $infos['nom'];
                $_SESSION['membre']['prenom'] = $infos['prenom'];
                $_SESSION['membre']['email'] = $infos['email'];
                $_SESSION['membre']['civilite'] = $infos['civilite'];
                $_SESSION['membre']['telephone'] = $infos['telephone'];
                $_SESSION['membre']['statut'] = $infos['statut'];
                
                
                // l'utilisateur est connecté, on le redirige vers profil.php
                header('location:profil.php');
                
            } else {
                // erreur sur le mdp
                $msg .= '<div class="alert alert-danger mb-3">Attention,<br>Erreur sur le pseudo et/ou le mot de passe.</div>';
            }

        } else {
            // erreur sur le pseudo
            $msg .= '<div class="alert alert-danger mb-3">Attention,<br>Erreur sur le pseudo et/ou le mot de passe.</div>';
        }



    }



include 'inc/header.inc.php'; // début des affichages dans la page !
include 'inc/nav.inc.php';
?>
        <main class="container">
            <div class="bg-light p-5 rounded">
                <h1><i class="fas fa-ghost indigo" ></i> Connexion <i class="fas fa-ghost indigo"></i></h1>
                <p class="lead">Bienvenue sur notre site.<hr><?php echo $msg; ?></p>                
            </div>

            <div class="row">
                <div class="col-4 mx-auto mt-5">
                    <!-- faire un formulaire avec les deux champs suivant : pseudo / mdp + un bouton de validation -->
                    <form method="post" >
                        <div class="mb-3">
                            <label for="pseudo" class="form-label">Pseudo <i class="text-primary fas fa-user-alt"></i></label>
                            <input type="text" class="form-control" id="pseudo" name="pseudo" value="<?php echo $pseudo; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="mdp"class="form-label">Mot de passe <i class="text-primary fas fa-user-alt"></i></label>
                            <input type="password" class="form-control" id="mdp" name="mdp">
                        </div>
                        <div class="mb-3 mt-4">
                            <button type="submit" class="btn btn-outline-primary w-100" id="connexion" ><i class="fas fa-keyboard"></i> Connexion <i class="fas fa-keyboard"></i></button>
                        </div>
                    </form>
                    <p class="text-center">Ou <br><a href="<?php echo URL; ?>inscription.php" class="btn btn-primary mt-2">Inscription</a><p>
                </div>
                
                    
                
            </div>
        </main>

<?php 
include 'inc/footer.inc.php';
 