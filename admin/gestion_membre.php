<?php
require_once("../inc/init.inc.php");

//---------------- VERIFICATION ADMIN
if(!internauteEstConnecteEtEstAdmin()) // si l'internaute n'est pas admin, il n'a rien à faire sur les pages d'administration
{
    header("location:" . URL . "connexion.php");
}
if($_POST)
{
if(isset($_GET['action']) && $_GET['action'] == 'modification')
{
    $resultat = $pdo->prepare("UPDATE membre SET id_membre= :id_membre, statut= :statut WHERE id_membre = :id_membre");
    $resultat->bindValue(':id_membre', $_POST['id_membre'], PDO::PARAM_INT);
    $resultat->bindValue(':statut', $_POST['statut'], PDO::PARAM_INT);
    $resultat->execute();

    $content .= '<div class="alert alert-success col-md-8 col-md-offset-2 text-center">Le membre : <span class="text-success">' . $_GET['id_membre'] . '</span> à bien été modifié dans la boutique.</div>';
}
}

//----- AFFICHAGE MEMBRES
$resultat = $pdo->query("SELECT id_membre,pseudo,nom,prenom,email,civilite,ville,code_postal,adresse,statut FROM membre");
if($resultat->rowCount() != 0)
{
$content .= '<div class="alert alert-success col-md-10 col-md-offset-1 text-center"><h3>Liste des membres</h3>';

$content .= 'Nombre de membre(s) dans la boutique : <span class="badge badge-success">' . $resultat->rowCount() . '</span></div>';

$content .= '<table class="table col-md-10"><tr>';
for($i = 0; $i < $resultat->columnCount(); $i++) // columnCount() est une méthode issu de la classe PDOStatement qui retourne le nombre de champs/colonnes de la table, tant qu'il y a des colonnes, on boucle
{
    $colonne = $resultat->getColumnMeta($i); // getColumnMeta() est une méhtode issu de la class PDOStatement qui récolte les informations des champs/colonnes de la table, pour chaque tour de boucle, $ colonne contient un tableau ARRAY avec les infos d'une colonne
    $content .= '<td><strong>' . $colonne['name'] . '</strong></td>'; // on va crocheter à l'indice 'name' pour afficher le nom des colonnes
}
$content .= '</tr>';
while($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) // on associe la méthode fetch() au résultat, $ligne contient un tableau ARRAY avec les informations d'un membre à chaque tour de boucle
    {
        $content .= '<tr>'; // on crée une nouvelle ligne du tableau pour chaque produit
        foreach($ligne as $indice => $informations) // passe en revu le tableau ARRAY d'un produit
    {
            $content .= "<td>$informations</td>";
    }
    $content .= '<td><a href="?action=modification&id_membre=' . $ligne['id_membre'] . '"><i class="fas fa-edit"></i></a></td>';
    $content .= '</tr>';
    }
    $content .= '</table>';
}



require_once("../inc/header.inc.php");
echo $content;

if(isset($_GET['action']) && $_GET['action'] == 'modification')
{
    if(isset($_GET['id_membre']))
    {
        $resultat = $pdo->prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
        $resultat->bindValue(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);
        $resultat->execute();

        $membre_actuel = $resultat->fetch(PDO::FETCH_ASSOC);
    }
    // si l'id_membre est définit dans la BDD, on l'affiche sinon on affiche une chaine de caractere vide
    $id_membre = (isset($membre_actuel['id_membre'])) ? $membre_actuel['id_membre'] :'';
    $statut = (isset($membre_actuel['statut'])) ? $membre_actuel['statut'] :'';
    
    //debug($_POST);
?>
<form method="post" action="" enctype="multipart/form-data" class="col-md-8 col-md-offset-2">
<h1 class="alert text-center"><?= ucfirst($_GET['action']); ?> membre</h1> 
    <input type="hidden" id="id_membre" name="id_membre" <?='value="' .  $id_membre . '"'?>>
    <div class="form-group">
    <label for="public">Statut</label>
    <select class="form-control" name="statut">
    <option value="0"<?php if($statut == '0') echo 'selected' ?>>Utilisateur</option>
    <option value="1"<?php if($statut == '1') echo 'selected' ?>>Admin</option> </select>
  </div>
  <button type="submit" class="btn btn-block btn-primary" Onclick="return(confirm(\'Êtes vous sur ?\'));">Modifier le membre</button>
</form>
<?php
}
require_once("../inc/footer.inc.php");
?>