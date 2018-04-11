<?php
//--------------------- CONNEXION BDD
$pdo = new PDO('mysql:host=localhost;dbname=boutique', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

//--------------------- SESSION
session_start();

//--------------------- CHEMIN
define("RACINE_SITE", $_SERVER['DOCUMENT_ROOT'] . "/front/Formation-Front/PHP/boutique/");
// cette constante retourne le chemin physique du dossier boutique sur le serveur lors de l'enregistrement de photos, nous auront besoin du chemin complet du dossier photo pour enregistrer une photo
// echo '<pre>'; print_r($_SERVER); echo '<pre>';
// echo RACINE_SITE;

define("URL", 'http://localhost/front/Formation-Front/PHP/boutique/');
// cette constante servira à enregistrer l'URL d'une photo/image dans la BDD, on ne conserve jamais la photo elle même, ce serait trop lourd pour la BDD

//-------------------- VARIABLES
$content = '';

//-------------------- INCLUSIONS
require_once("fonction.inc.php");