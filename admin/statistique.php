<?php
include '../inc/init.inc.php';
include '../inc/function.inc.php';

if (user_is_admin() == false) {
    header('location:../index.php');
}
// membres les mieux notés
$liste_membre = $pdo->query("SELECT moyenne, pseudo FROM membre GROUP BY pseudo ORDER BY moyenne DESC LIMIT 5");
// membres les plus actifs
$membreActif = $pdo->query("SELECT COUNT(id_annonce) AS nb, pseudo FROM annonce, membre WHERE membre_id = id_membre GROUP BY pseudo ORDER BY nb DESC");
// annonces les plus anciennes
$liste_annonces = $pdo->query("SELECT id_annonce, titre, DATE_FORMAT(date_enregistrement, '%d/%m/%Y') AS date FROM annonce ORDER BY date_enregistrement ASC LIMIT 5");
// categories contenant le plus d'annonce
$liste_categorie = $pdo->query("SELECT id_categorie, categorie.titre, COUNT(*) AS nb FROM categorie AS categorie, annonce AS annonce WHERE categorie.id_categorie = annonce.categorie_id GROUP BY categorie.titre ORDER BY COUNT(*) DESC LIMIT 5");


include '../inc/header.inc.php';
include '../inc/nav.inc.php';
?>

<main class="container">

    <div class="bg-light p-5 rounded ">
        <h1 class="text-center">Statistiques <i class="fas fa-chart-line"></i></h1>
        <p class="lead text-center">
            <hr><?php echo $msg; ?>
        </p>
    </div>

    <div class="row">
        <div class="col-sm-6 mt-5">
            <h4>Top 5 des membres les mieux notés</h4>
            <?php
            while ($membre = $liste_membre->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="mt-5">';
                echo '<ul><li>' . $membre['pseudo'] . ' <span class="px-3 py-1 border rounded-pill alert-danger" > ' . round($membre['moyenne'], 2) . '  <span></li></ul>';
                echo ' </div>';
            }
            ?>
        </div>
        <div class="col-sm-6 mt-5">
            <h4>Top 5 des membres les plus actifs</h4>
            <?php
            while ($actif = $membreActif->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="mt-5">';
                echo '<ul><li>' . $actif['pseudo'] . ' <span class="px-3 py-1 border rounded-pill alert-danger" > ' . $actif['nb'] . ' annonces en cours <span></li></ul>';
                echo ' </div>';
            }
            ?>
        </div>
        <div class="col-sm-6 mt-5">
            <h4>Top 5 des annonces les plus anciennes</h4>
            <?php
            while ($annonce = $liste_annonces->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="mt-5">';
                echo '<ul><li>' . $annonce['titre'] . ' - ID : ' . $annonce['id_annonce'] . '     <br><span class="px-3 py-1 border rounded-pill alert-danger"> publiée depuis le ' . $annonce['date'] . ' <span></li></ul>';

                echo ' </div>';
            }
            ?>
        </div>
        <div class="col-sm-6 mt-5">
            <h4>Top 5 des categories contenant le plus d'annonce</h4>
            <?php

            while ($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="mt-5">';
                echo '<ul><li>' . $categorie['titre'] . ' - ID : ' . $categorie['id_categorie'] . '     <span class="px-3 py-1 border rounded-pill alert-danger"> ' . $categorie['nb'] . ' annonce(s) dans cette catégorie <span></li></ul>';
                echo ' </div>';
            }
            ?>
        </div>
    </div>
</main>

<?php
include '../inc/footer.inc.php';
