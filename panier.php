<?php 
require_once("inc/init.inc.php");

//------ AJOUT PANIER ------//
if(isset($_POST['ajout_panier']))
{
    //debug($_POST);
    $resultat = $pdo->query("SELECT * FROM produit WHERE id_produit = '$_POST[id_produit]'");
    $produit = $resultat->fetch(PDO::FETCH_ASSOC);
    //debug($produit);
    ajouterProduitPanier($produit['titre'], $_POST['id_produit'], $_POST['quantite'], $produit['prix'], $produit['description'], $produit['photo']);
    //debug($_SESSION);
}

//--------- SUPPRESSION PRODUIT ----------//
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && $_GET['id_produit']) // on ne rentre ici seulement dans le cas où l'on clique sur le lien supprimer
{
    retirerProduitDuPanier($_GET['id_produit']); // on execute la fonction permettant de supprimer

    $resultat = $pdo->prepare("SELECT * FROM produit WHERE id_produit= :id_produit");
    $resultat->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
    $resultat->execute();
    $produit_supp = $resultat->fetch(PDO::FETCH_ASSOC);

    $content .= '<hr><div class="alert alert-success text-center">Le produit : <strong>' . $produit_supp['titre'] . '</strong> a bien été supprimé du panier.</div>';
}

//---- VIDER PANIER -------//
// réaliser le script permettant de vider le panier
if(isset($_GET['action']) && $_GET['action'] == 'vider') // si on a cliqué sur le lien 'vider', on rentre dans la condition
{
    unset($_SESSION['panier']); // on supprime seulement le tableau 'panier' de la session
}

//-------- PAIEMENT ---------//
if(isset($_POST['payer']))
{
    for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
    {
        $resultat = $pdo->query("SELECT * FROM produit WHERE id_produit=" . $_SESSION['panier']['id_produit'][$i]);
        $produit = $resultat->fetch(PDO::FETCH_ASSOC);
        $erreur = '';
        if($produit['stock'] < $_SESSION['panier']['quantite'][$i])
        {
            $content .= '<hr><div class="alert alert-danger">Stock restant du produit <strong>' . $_SESSION['panier']['titre'][$i] . '</strong> : ' . $produit['stock'] . '</div>';
            $content .= '<hr><div class="alert alert-danger">Quantité demandé : <strong>' . $_SESSION['panier']['titre'][$i] . '</strong> : ' . $_SESSION['panier']['quantite'][$i] . '</div>';

            if($produit['stock'] > 0)
            {
                $content .= '<hr><div class="alert alert-danger text-center">La quantité du produit <strong>' . $_SESSION['panier']['titre'][$i] . '</strong> a été réduite car notre stock est insuffisant, veuillez vérifier vos achats!</div>';
                $_SESSION['panier']['quantite'][$i] = $produit['stock'];
            }
            else
            {
                $content .= '<hr><div class="alert alert-danger text-center">Le produit <strong>' . $_SESSION['panier']['titre'][$i] . '</strong> a été supprimé de la boutique car nous sommes en rupture de stock, veuillez vérifier vos achats!</div>';

                retirerProduitDuPanier($_SESSION['panier']['id_produit'][$i]);
                $i--;
            }
            $content .= $erreur;
        }
    }

    if(empty($erreur))
    {
        $resultat = $pdo->exec("INSERT INTO commande(id_membre, montant, date_enregistrement) VALUES(" . $_SESSION['membre']['id_membre'] . "," . montantTotal() . ", NOW())");
        $id_commande = $pdo->lastInsertId();
        //debug($id_commande);
        for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
        {
            $resultat = $pdo->exec("INSERT INTO details_commande(id_commande,id_produit,quantite,prix) VALUES($id_commande, " . $_SESSION['panier']['id_produit'][$i] . "," . $_SESSION['panier']['quantite'][$i] . "," . $_SESSION['panier']['prix'][$i] . ")");

            $resultat = $pdo->exec("UPDATE produit SET stock = stock - " . $_SESSION['panier']['quantite'][$i] . " WHERE id_produit = " .$_SESSION['panier']['id_produit'][$i]);
        }
        unset($_SESSION['panier']);
        $content .= '<hr><div class="alert alert-success text-center">Votre commande a bien été validé, votre numéro de suivi est le : <strong>' . $id_commande . '</strong></div>';
    }
}

require_once("inc/header.inc.php");
echo $content;
?>

<div class="col-md-12">
    <table id="cart" class="table table-hover table-condensed">
        <thead>
            <tr>
                <th style="width:55%">Produit</th>
                <th style="width:10%">Prix unitaire</th>
                <th style="width:8%" class="text-right">Quantité</th>
                <th style="width:22%" class="text-center">Prix total</th>
                <th style="width:5%">Supprimer</th>
            </tr>
        </thead>
            <?php
                if(empty($_SESSION['panier']['id_produit']))
                {
                    echo '<tr><td colspan="5"><div class="alert alert-danger text-center">Votre panier est vide.</div></td></tr>';
                }
                else
                {
                echo '<tbody>';
                echo '<tr>';
                for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
                {
            ?>
                <td data-th="Product">
                    <div class="row">
                        <div class="col-sm-2"><img src="<?= $_SESSION['panier']['photo'][$i] ?>" alt="..." class="img-responsive"/></div>
                        <div class="col-sm-10">
                            <h4 class="nomargin"><?= $_SESSION['panier']['titre'][$i] ?></h4>
                            <p><?= $_SESSION['panier']['description'][$i] ?></p>
                        </div>
                    </div>
                </td>
                <td><?= $_SESSION['panier']['prix'][$i] ?></td>
                <td class="text-right">
                    <?= $_SESSION['panier']['quantite'][$i] ?>
                </td>
                <td data-th="Subtotal" class="text-center"><?= $_SESSION['panier']['prix'][$i]*$_SESSION['panier']['quantite'][$i] ?> €</td>
                <td class="actions" data-th="">
                    <a href="?action=suppression&id_produit=<?= $_SESSION['panier']['id_produit'][$i] ?>"><button class="btn btn-danger btn-sm col-md-offset-3"><i class="glyphicon glyphicon-remove"></i></button></a>							
                </td>
            </tr>
        </tbody>
        <?php }} ?>
        <tfoot>
            <tr class="visible-xs">
                <td class="text-center"><strong></strong></td>
            </tr>
            <tr>
                <td><a href="boutique.php" class="btn btn-primary">Continuer shopping</a></td>
                <td><a href="?action=vider"><button class="btn btn-danger btn-sm">Vider le panier <i class="glyphicon glyphicon-trash"></i></button></a></td>
                <td colspan="1" class="hidden-xs"></td>
                <td class="hidden-xs text-center"><strong><?php if(empty($_SESSION['panier']['id_produit'])) {echo '';} else {echo montantTotal() . ' €';} ?></strong></td>
                
                <?php
                if(internauteEstConnecte())
                {
                    echo '<form method="post" action="">';
                    echo '<td><input name="payer" type="submit" value="payer" class="btn btn-success btn-block"></td>';
                    echo '</form>';
                }
                else
                {
                    echo '<td><a href="#" class="btn btn-success btn-block">Veuillez vous connecter</a></td>';
                }

                ?>

            </tr>
        </tfoot>
    </table>
</div>


<?php
require_once("inc/footer.inc.php");
