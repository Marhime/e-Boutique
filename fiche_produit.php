<?php 
require_once("inc/init.inc.php");
require_once("inc/header.inc.php");
?>
<div class="row">
  <ul class="pager col-md-2">
    <li><a href="boutique.php">Revenir à la boutique</a></li>
  </ul>
</div>
<div class="row fiche-produit">
 <?php if(isset($_GET['id_produit'])):
                    $donnees = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
                    $donnees->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
                    $donnees->execute();

                    if($donnees->rowCount() <= 0)
                    {
                        header("location:boutique.php");
                        exit();
                    }

                    while($produit = $donnees->fetch(PDO::FETCH_ASSOC)):
            ?>
                <div class="col-xs-12 col-md-6 item-photo">
                    <img style="max-width:100%;" src="photo/<?= $produit['photo'] ?>" />
                </div>
                <div class="col-xs-12 col-md-6">
                    <!-- Titre du produit -->
                    <h1><small><a href="boutique.php?categorie=<?= $produit['categorie'] ?>"><?= $produit['categorie'] ?></a></small></h1>
                    <h1><?= $produit['titre'] ?></h1>
                    <hr>

                    <!-- Prix -->
                    <div class="row">
                        <div class="col-md-4">
                            <h3><small>Couleur</small></h3>
                            <h3 style="margin-top:0px;"><?= $produit['couleur'] ?></h3>
                        </div>
                        <div class="col-md-5 col-md-offset-1">
                            <h3><small>Prix</small></h3>
                            <h3 style="margin-top:0px;"><?= $produit['prix'] ?> Euros</h3>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-4">
                        <?php if($produit['stock'] > 0) { ?>
                            <h3><small>Quantité</small></h3>
                                <form method="post" action="panier.php">
                                <select class="form-control" id="quantite" name="quantite">
                                <?php
                                for($i = 1; $i <= $produit['stock'] && $i <= 5; $i++)
                                {
                                    echo "<option>$i</option>"; 
                                }
                                ?>
                                </select>
                                <input type="hidden" name="id_produit" value="<?= $produit['id_produit'] ?>">
                                <input style="margin-top:20px" type="submit" name="ajout_panier" class="btn btn-primary col-md-12" value="Ajouter au panier">
                                </form></div>
                                <div class="col-md-4 col-md-offset-1">
                                <h3><small>Produit en stock</small></h3>
                                <h3 style="margin-top:0px;"><?= $produit['stock']?></h3></div>
                                <?php
                                } 
                                else 
                                {
                                echo '</div><h3><span class="label label-danger">Produit épuisé</span></h3>';
                                }
                                ?>
                                </div>
                    
                                </div>
                    <!-- Details produit -->
                    <div class="col-md-6">
                    <h3><small>Description</small></h3>
                        <p><?= $produit['description'] ?></p>
                    </div>
                                                          
                </div>
            </div>
    </div>
                    <?php endwhile; endif; ?>
</div>
            <?php 
    require_once ("inc/footer.inc.php");
        //<i class="glyphicon glyphicon-shopping-cart"></i>
    ?>

