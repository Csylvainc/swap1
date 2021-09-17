<?php
include 'inc/init.inc.php';
include 'inc/function.inc.php';


$requeteFiltre = "";
if($_GET['categorie']!='Tous'){
  $requeteFiltre.= 'categorie_id = :categorie AND ';
}
if($_GET['membre']!='Tous'){
  $requeteFiltre.= 'membre_id = :membre AND ';
}
if($_GET['region']!='Tous'){
  $requeteFiltre.= 'region = :region AND ';
}


// il est toujours là 
$requeteFiltre.='prixMax<=:prix ';




if(($_GET['ordre']=='prix' || $_GET['ordre']=='etoile' || $_GET['ordre']=='date') && ($_GET['ASCDESC']=='ASC' || $_GET['ASCDESC']=='DESC')){
    $requeteFiltre.= 'ORDER BY '.$_GET['ordre']. ' ' .$_GET['ASCDESC'];
}


$liste_annonces = $pdo->prepare("SELECT *, annonce.titre AS titre_a, categorie.titre AS titre_c FROM annonce AS annonce  INNER JOIN categorie AS categorie ON annonce.categorie_id = categorie.id_categorie WHERE ".$requeteFiltre);
if($_GET['categorie']!='Tous'){$liste_annonces->bindParam(':categorie', $_GET['categorie'], PDO::PARAM_STR);}
if($_GET['membre']!='Tous'){$liste_annonces->bindParam(':membre', $_GET['membre'], PDO::PARAM_STR);}
if($_GET['region']!='Tous'){$liste_annonces->bindParam(':region', $_GET['region'], PDO::PARAM_STR);}

$liste_annonces->execute();


$annonce = $liste_annonces->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($annonce);
