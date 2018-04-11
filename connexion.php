<?php  
require_once ("inc/init.inc.php");

if(internauteEstConnecte()) // si l'internaute n'est pas connecté, il n'a rien à faire sur la page profil, on le redigire vers la page connexion
{
    header("location:profil.php");
}
if(isset($_GET['action'])&& $_GET['action']== 'deconnexion') // si on clique sur le lien deconnexion on supprimer la session
{
    session_destroy();
}
//debug($_POST)
if($_POST)
{
    $connexion = $pdo->query("SELECT * FROM membre WHERE pseudo = '$_POST[pseudo]'"); //on selectionne en BDD tous les membres qui possède le même pseudo que l'internaute a saisie dans le formulaire

    if($connexion->rowCount() != 0) // si le resultat est différent de 0, c'est que le pseudo est connu en BDD
    {
        $membre = $connexion->fetch(PDO::FETCH_ASSOC); //on associe la methode fetch() pour rendre exploitable le résultat et récupérer les données de l'internaute ayant saisi le bon pseudo
        //debug($membre);
        // if($membre['mdp'] == $_POST['mdp'])
        if(password_verify($_POST['mdp'], $membre['mdp'])) // on controle que le mot de passe saisie par l'internaute est le même qui celui présent en BDD
        {
            $content .= '<div class="alert alert-success col-md-8 col-md-offset-2 text-center">ACHETE !!</div>';
            foreach($membre as $indice => $valeur) // on passe en revue les informations du membre qui a le bon mdp
            {
                if($indice != 'mdp') // on exclu le mdp qui n'est pas conservé dans le fichier session
                {
                    $_SESSION['membre'][$indice] = $valeur; // on crée dans le fichier session un tableau membre et on enregistre les données de l'internaute qui ppourra dès à présent naviguer sur le site sans être déconnecté
                }
            }
            //debug($_SESSION);
            header("location:profil.php"); // ayant les bons identifiants on le redirige vers sa page profil
        }
        else // sinon l'internaute a saisie un mauvais mdp
        {
            $content .= '<div class="alert alert-danger col-md-8 col-md-offset-2 text-center">Mauvais mot de passe!</div>';
        }
    }
    else
    {
        $content .= '<div class="alert alert-danger col-md-8 col-md-offset-2 text-center">Casse toi pauv\' con!</div>';
    }
}
require_once("inc/header.inc.php");
echo $content;
?>
<form method="post" action="" class="col-md-4 col-md-offset-4 connexion">
<h1 class="alert text-center">Connexion</h1>
  
  <div class="form-group">
    <label for="pseudo">Pseudo</label>
    <input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="pseudo">
  </div>
  <div class="form-group">
    <label for="mdp">Mot de passe</label>
    <input type="password" class="form-control" id="mdp" name="mdp" placeholder="mot de passe">
  </div>
  <div class="form-group">
  <button type="submit" class="btn btn-primary col-md-offset-4">Connexion</button>
  </div>
</form>
<?php
require_once ("inc/footer.inc.php");
?>