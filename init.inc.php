<?php
// connexion à la BDD
$host = 'mysql:host=localhost;dbname=szn_mysql'; 
$login = 'szn_mysql'; // login
$password = '6JxjiQM5'; // mdp
$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::ATTR_DEFAULT_STR_PARAM => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' 
);
$pdo = new PDO($host, $login, $password, $options);

// création d'une variable que l'on appelle sur toutes les pages. Elle nous permet d'evoyer des messages à l'utilisateur en cas de besoin
$msg = '';

// création /ouverture de la session
session_start();


//Déclaration de constantes
// url absolue
define('URL', 'http://sylvaincampos.lescigales.org/'); // à modifier lors de la mise en ligne
// chemin racine serveur pour l'enregistrement des fichiers chargés via le formulaire
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
// chemin depuis le serveur vers notre site
define('PROJECT_PATH', '//'); // à modifier lors de la mise en ligne