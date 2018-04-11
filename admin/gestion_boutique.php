<?php
require_once("../inc/init.inc.php");

//---------------- VERIFICATION ADMIN
if(!internauteEstConnecteEtEstAdmin()) // si l'internaute n'est pas admin, il n'a rien à faire sur les pages d'administration
{
    header("location:" . URL . "connexion.php");
}

//--------------- SUPPRESSION PRODUIT


if(isset($_GET['action']) && $_GET['action'] == 'suppression')
{
    $resultat = $pdo->exec("DELETE FROM produit WHERE id_produit= $_GET[id_produit];");
    $_GET['action'] = 'affichage';
    $content .= '<div class="alert alert-success col-md-8 col-md-offset-2 text-center">Le produit n° <span class="text-success">' . $_GET['id_produit'] . '</span> a bien été supprimé.</div>';
}


//---------------- ENREGISTREMENT PRODUIT
if(!empty($_POST))
    {
        $photo = '';
        if(isset($_GET['action']) && $_GET['action'] == 'modification')
        {
            $photo_bdd = $_POST['photo_actuelle']; // si on souhaite conserver la même photo en cas de modification, on affecte la valeur du champs photo 'hidden', c'est à dire l'URL de la photo déjà en BDD
        }
            // debug($_FILES);
            if(!empty($_FILES['photo']['name']))
        {
            $nom_photo = $_POST['reference'] . '-' . $_FILES['photo']['name'];
            //echo $nom_photo;
            $photo_bdd = URL . "photo/$nom_photo"; // on définit l'URL de la photo
            //echo $photo_bdd;
            $photo_dossier = RACINE_SITE . "photo/$nom_photo"; // On définit le chemin physique du dossier
            echo $photo_dossier;
            copy($_FILES['photo']['tmp_name'], $photo_dossier); // on copie la photo directement dans le dossier photo. La fonction copy() reçoit 2 arguments : 1 - le nom temporaire -2 le chemin du dossier photo
        }
        if(isset($_GET['action']) && $_GET['action'] == 'ajout')
        {
            // Exercice : réaliser le script permettant de contrôler la disponibilité de la référence
            $erreur = '';
            $verif_ref = $pdo->prepare("SELECT * FROM produit WHERE reference = :reference");
            $verif_ref->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
            $verif_ref->execute();
            if($verif_ref->rowCount() > 0)
            {
                $erreur .= '<div class="alert alert-danger col-md-8 col-md-offset-2 text-center">La référence existe déjà dans la boutique. Merci de saisir une référence valide !</div>';
            }
            $content .= $erreur;

            if(empty($erreur)) // on stock les messages d'erreur dans la variable $erreur, si elle est vide, cela veut dire que nous ne sommes pas rentré dans la condition if et donc notre référence est inutilisé (valide)
            {
                $resultat = $pdo->prepare("INSERT INTO produit (reference, categorie, titre, description, couleur, taille, public, photo, prix, stock) VALUES (:reference,:categorie,:titre,:description,:couleur,:taille,:public,:photo,:prix,:stock);");
                $content .= '<div class="alert alert-success col-md-8 col-md-offset-2 text-center">Votre produit référence : <span class="text-success">' . $_POST['reference'] . '</span> à bien été enregistré dans la boutique.</div>';
            }
         }
            else
            {
                $resultat = $pdo->prepare("UPDATE produit SET reference = :reference, categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, public = :public, photo = :photo, prix = :prix, stock = :stock WHERE id_produit = '$_POST[id_produit]';");
                $content .= '<div class="alert alert-success col-md-8 col-md-offset-2 text-center">Votre produit référence : <span class="text-success">' . $_POST['reference'] . '</span> à bien été modifié dans la boutique.</div>';
            }

        if(empty($erreur)){
            $resultat->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
            $resultat->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
            $resultat->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
            $resultat->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
            $resultat->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
            $resultat->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
            $resultat->bindValue(':public', $_POST['public'], PDO::PARAM_STR);
            $resultat->bindValue(':photo', $photo_bdd, PDO::PARAM_STR);
            $resultat->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
            $resultat->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);
            $resultat->execute();
        }
    }
//----- LIENS PRODUITS

$content .= '<div class="list-group col-md-6 col-md-offset-3">';
$content .= '<h3 class="list-group-item active text-center">BACK OFFICE</h3>';
$content .= '<a href="?action=affichage" class="list-group-item text-center">Affichage produits</a>';
$content .= '<a href="?action=ajout" class="list-group-item text-center">Ajout produit</a>';
$content .= '<hr></div>';

//----- AFFICHAGE PRODUITS
if(isset($_GET['action']) && $_GET['action'] == 'affichage')
{
// Exercice : afficher toute la table produit sous forme de tableau HTML, prévoir un lien modification et suppression
$resultat = $pdo->query("SELECT * FROM produit");
if($resultat->rowCount() != 0)
{
$content .= '<div class="alert alert-success col-md-10 col-md-offset-1 text-center"><h3>Affichage produits</h3>';

$content .= 'Nombre de produit(s) dans la boutique : <span class="badge badge-success">' . $resultat->rowCount() . '</span></div>';

$content .= '<table class="table"><tr>';
for($i = 0; $i < $resultat->columnCount(); $i++) // columnCount() est une méthode issu de la classe PDOStatement qui retourne le nombre de champs/colonnes de la table, tant qu'il y a des colonnes, on boucle
{
    $colonne = $resultat->getColumnMeta($i); // getColumnMeta() est une méhtode issu de la class PDOStatement qui récolte les informations des champs/colonnes de la table, pour chaque tour de boucle, $ colonne contient un tableau ARRAY avec les infos d'une colonne
    $content .= '<td><strong>' . $colonne['name'] . '</strong></td>'; // on va crocheter à l'indice 'name' pour afficher le nom des colonnes
}
$content .= '</tr>';
while($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) // on associe la méthode fetch() au résultat, $ligne contient un tableau ARRAY avec les informations d'un produit à chaque tour de boucle
    {
        $content .= '<tr>'; // on crée une nouvelle ligne du tableau pour chaque produit
        foreach($ligne as $indice => $informations) // passe en revu le tableau ARRAY d'un produit
    {
        if($indice == 'photo')
        {
            $content .= '<td><img src="' . $informations . '" class="img-responsive"></td>';
        }
        else
        {
            $content .= "<td>$informations</td>";
        }
    }
    $content .= '<td><a href="?action=modification&id_produit=' . $ligne['id_produit'] . '"><i class="fas fa-edit"></i></a></td>';
    $content .= '<td><a href="?action=suppression&id_produit=' . $ligne['id_produit'] . '"Onclick="return(confirm(\'Êtes vous sur ?\'));"><i class="fas fa-trash-alt"></i></a></td>';
    $content .= '</tr>';
    }
    $content .= '</table>';
}
else
{
    if(isset($_GET['id_produit']))
    {
        $content .= '<div class="alert alert-success col-md-8 col-md-offset-2 text-center">Le produit n° <span class="text-success">' . $_GET['id_produit'] . '</span> a bien été supprimé.</div>';
    }
    $content .= '<div class="alert alert-danger col-md-10 col-md-offset-1 text-center">Produit actuellement dans votre boutique ' . $resultat->rowCount() . '<br> <strong><a href="?action=ajout"><i class="fas fa-plus-square"></i> ajouter un produit</a></strong></div>';
}
}
require_once("../inc/header.inc.php");
echo $content;
?>

<!-- Réaliser un formulaire HTML correspondant à la table produit de la BDD -->
<?php
if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification'))
{
    if(isset($_GET['id_produit']))
    {
        $resultat = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
        $resultat->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
        $resultat->execute();

        $produit_actuel = $resultat->fetch(PDO::FETCH_ASSOC);
        //debug($produit_actuel);
        
    }
    // si l'id_produit est définit dans la BDD, on l'affiche sinon on affiche une chaine de caractere vide
    $id_produit = (isset($produit_actuel['id_produit'])) ? $produit_actuel['id_produit'] :'';
    $reference = (isset($produit_actuel['reference'])) ? $produit_actuel['reference'] :'';
    $categorie = (isset($produit_actuel['categorie'])) ? $produit_actuel['categorie'] :'';
    $titre = (isset($produit_actuel['titre'])) ? $produit_actuel['titre'] :'';
    $description = (isset($produit_actuel['description'])) ? $produit_actuel['description'] :'';
    $couleur = (isset($produit_actuel['couleur'])) ? $produit_actuel['couleur'] :'';
    $public = (isset($produit_actuel['public'])) ? $produit_actuel['public'] :'';
    $taille = (isset($produit_actuel['taille'])) ? $produit_actuel['taille'] :'';
    $photo = (isset($produit_actuel['photo'])) ? $produit_actuel['photo'] :'';
    $prix = (isset($produit_actuel['prix'])) ? $produit_actuel['prix'] :'';
    $stock = (isset($produit_actuel['stock'])) ? $produit_actuel['stock'] :'';
?>
<form method="post" action="" enctype="multipart/form-data" class="col-md-8 col-md-offset-2">
<h1 class="alert text-center"><?= ucfirst($_GET['action']); ?> produit</h1> 
    <input type="hidden" id="id_produit" name="id_produit" <?='value="' .  $id_produit . '"'?>>
  <div class="form-group">
    <label for="reference">Références</label>
    <input type="text" class="form-control" id="reference" name="reference" <?php echo 'value="' .  $reference . '"'; ?> placeholder="Références">
  </div>
  <div class="form-group">
    <label for="categorie">Catégorie</label>
    <input type="text" class="form-control" id="categorie" name="categorie" <?='value="' .  $categorie . '"'?> placeholder="Catégorie">
  </div>
  <div class="form-group">
    <label for="titre">Titre</label>
    <input type="text" class="form-control" id="titre" name="titre" <?='value="' .  $titre . '"'?> placeholder="Titre">
  </div>
  <div class="form-group">
    <label for="description">Description</label>
    <input type="text" class="form-control" id="description" name="description" <?='value="' .  $description . '"'?> placeholder="Description">
  </div>
  <div class="form-group">
    <label for="couleur">Couleur</label>
    <input type="text" class="form-control" id="couleur" name="couleur" <?='value="' . $couleur . '"'?> placeholder="Couleur">
  </div>
  <div class="form-group">
    <label for="public">Public</label>
    <select class="form-control" name="public">
    <option value="m"<?php if($public == 'm') echo 'selected' ?>>Homme</option>
    <option value="f"<?php if($public == 'f') echo 'selected' ?>>Femme</option>
    <option value="mixte"<?php if($public == 'mixte') echo 'selected' ?>>Mixte</option>
    </select>
  </div>
  <div class="form-group">
    <label for="taille">Taille</label>
    <select class="form-control" name="taille">
    <option value= "xs"<?php if($taille == 'xs') echo 'selected' ?>>XS</option>
    <option value="s"<?php if($taille == 's') echo 'selected' ?>>S</option>
    <option value="m"<?php if($taille == 'm') echo 'selected' ?>>M</option>
    <option value="l"<?php if($taille == 'l') echo 'selected' ?>>L</option>
    <option value="xl"<?php if($taille == 'xl') echo 'selected' ?>>XL</option>
    </select>
  </div>
  <div class="form-group">
    <label for="photo">Photo</label>
    <input type="file" class="form-control" id="photo" name="photo" <?='value="' .  $photo . '"'?> placeholder="Photo"><br>
    <?php
    if(!empty($photo))
    {
        echo '<i>Vous pouvez uploader une nouvelle photo</i><br>';
        echo'<img src="' . $photo . '" class="img-responsive" value="' . $photo . '"><br>';
    }
    echo '<input type="hidden" id="photo_actuelle" name="photo_actuelle" value="' . $photo . '">';
    ?>
  </div>
  <div class="form-group">
    <label for="prix">Prix</label>
    <input type="number" class="form-control" id="prix" name="prix" <?='value="' . $prix . '"'?> placeholder="Prix">
  </div>
  <div class="form-group">
    <label for="stock">Stock</label>
    <input type="text" class="form-control" id="stock" name="stock" <?='value="' . $stock . '"'?> placeholder="Stock">
  </div>
  <button type="submit" class="btn btn-block btn-primary"><?php if($_GET['action'] == 'ajout') {echo ucfirst($_GET['action']). 'er un produit';} if($_GET['action'] == 'modification'){echo 'Modifier le produit';} ?></button>
</form>

<?php
}
require_once("../inc/footer.inc.php");
?>