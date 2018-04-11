<?php  
require_once("inc/init.inc.php");
require_once("inc/header.inc.php");
if(!internauteEstConnecte()) // si l'internaute n'est pas connecté, il n'a rien à faire sur la page profil, on le redigire vers la page connexion
{
    header("location:connexion.php");
}
?>

<div class="col-md-8 col-md-offset-2 panel-default border">
    <div class="panel-heading text-center">
        <h1>PROFIL</h1>
    </div>
    <div class="col-md-12 text-center">
        <h2>Bonjour <span class="text-danger"><?= $_SESSION['membre']['pseudo']?></span></h2>
        <ul class="list-unstyled">
            <li><h3>Voici les informations de votre profil</h3></li>
            <li>Nom : <?= $_SESSION['membre']['prenom']; ?></li>
            <li>Prénom : <?= $_SESSION['membre']['nom']; ?></li>
            <li>Email : <?= $_SESSION['membre']['email']; ?></li>
            <li>Adresse : <?= $_SESSION['membre']['adresse']; ?></li>
            <li>Ville : <?= $_SESSION['membre']['ville']; ?></li>
            <li>Code postal : <?= $_SESSION['membre']['code_postal']; ?></li>
            <!-- on se sert du fichier session pour afficher les données de l'internaute -->
        </ul>
    </div>
</div>

<?php
require_once ("inc/footer.inc.php");
?>