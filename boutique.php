<?php 
require_once("inc/init.inc.php");
require_once("inc/header.inc.php");
?>

    <div class="row row-offcanvas row-offcanvas-right">
        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
            <div class="list-group">
                <p href="#" class="list-group-item active text-center">CATÉGORIES</p>
                <?php
                $resultat = $pdo->query("SELECT DISTINCT categorie FROM produit");
                while($categorie = $resultat->fetch(PDO::FETCH_ASSOC))
                foreach($categorie as $indice => $valeur)
                {
                    echo '<a href="?categorie=' . $valeur . '" class="list-group-item">' . $valeur . '</a>';
                }
                ?>
            </div>
        </div>
        <!--/.sidebar-offcanvas-->

        <div class="col-xs-12 col-sm-9">
            <p class="pull-right visible-xs">
                <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
            </p>
            <div class="jumbotron">
                <h1>Bienvenue sur notre boutique.</h1>
                <p>Achetez sans modération !</p>
            </div>
            <div class="row">
            <?php if(isset($_GET['categorie'])):
                    $donnees = $pdo->prepare("SELECT * FROM produit WHERE categorie = :categorie");
                    $donnees->bindValue(':categorie', $_GET['categorie'], PDO::PARAM_INT);
                    $donnees->execute();

                    while($produit = $donnees->fetch(PDO::FETCH_ASSOC)):
            ?>


                <div class="col-xs-6 col-lg-4 thumb-produit">
                    <div class="panel-heading text-center"><h2><?= $produit['titre'] ?></h2><br>
                        <?= '<img class="img-responsive" src="photo/' . $produit['photo'] . '">'?>
                        <?= '<span class="label label-success col-lg-offset-8">' . $produit['prix'] . ' Euros</span>' ?>
                    </div>
                    <p><?= substr($produit['description'], 0, 150) ?>...</p>
                    <p><a class="btn btn-primary col-lg-offset-2" href="fiche_produit.php?id_produit=<?= $produit['id_produit'] ?>" role="button">En savoir plus...</a></p>
                </div>
           
                    <?php endwhile; endif; ?>
                    </div>
            <!--/row-->
        </div>
        <!--/.col-xs-12.col-sm-9-->

    </div>
    <!--/row-->
    <?php 
    require_once ("inc/footer.inc.php");
    ?>