<?php

function debug ($var, $mode = 1)
{
    echo '<div style = "background: orange; padding:5px;">';
    $_trace = debug_backtrace(); // fonction prédéfinie retourne un ARRAY contenant des infos telle que la ligne et le fichier où est exécuté la fonction.
    // echo '<pre>'; print_r($_trace); echo '</pre>';
    $_trace = array_shift($_trace);
    // echo '<pre>'; print_r($_trace); echo '</pre>';
    echo "Debug demandé dans le fichier : $_trace[file] à la ligne $_trace[line]. <hr>";
    
    if($mode === 1)
    {
        echo '<pre>'; print_r($var); echo '</pre>';
    }
    else
    {
        echo '<pre>'; var_dump($var); echo '</pre>';
    }
    echo '</div>';
}
// debug();

//----------------------------
function internauteEstConnecte()
{
    if(!isset($_SESSION['membre']))
    {
        return false;
    }
    else
    {
        return true;
    }

}

//----------------------------
function internauteEstConnecteEtEstAdmin()
{
    if(internauteEstConnecte() && $_SESSION['membre']['statut'] == 1) // si la session du membre est définie et que son statut est égal à 1, cela veut dire qu'il est admin, on retourne true
    {
        return true;
    }
    else
    {
        return false;
    }
}

//--------------- PANIER -----------------//
function creationDuPanier()
{
    if(!isset($_SESSION['panier'])) // si l'indice panier dans la session n'est pas défini, c'est que l'internaute n'a pas de panier actuellement dans sa session, donc on peut créer son panier
    {
        $_SESSION['panier'] = array();
        $_SESSION['panier']['titre'] = array();
        $_SESSION['panier']['id_produit'] = array();
        $_SESSION['panier']['quantite'] = array();
        $_SESSION['panier']['produit'] = array();
        $_SESSION['panier']['description'] = array();
        $_SESSION['panier']['photo'] = array();
    }
}

//-------------------------------------
function ajouterProduitPanier($titre,$id_produit,$quantite,$prix,$description,$photo) // fonction utilisateur recevant 4 arguments qui seront conservé dans la session 'panier'
{
    creationDuPanier(); // on contrôle si le panier existe ou non dans la session

    $position_produit = array_search($id_produit, $_SESSION['panier']['id_produit']); // on contrôle grace à la fonction prédéfinie array_search si le produit est déjà dans le panier de la session et à quel indice
    echo $position_produit;

    if($position_produit !== false)
    {
        $_SESSION['panier']['quantite'][$position_produit] += $quantite; // on change à l'indice trouvé la quantité
    }
    else
    {
    $description = substr($description, 0, 150) . '...';
    $_SESSION['panier']['titre'][] = $titre; // les [] vide permettent de créer par défaut des indices numérique pour les données
    $_SESSION['panier']['id_produit'][] = $id_produit;
    $_SESSION['panier']['quantite'][] = $quantite;
    $_SESSION['panier']['prix'][] = $prix;
    $_SESSION['panier']['description'][] = $description;
    $_SESSION['panier']['photo'][] = $photo;
    }
}

function montantTotal()
{
    $total = 0;
    for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
    {
        $total += $_SESSION['panier']['quantite'][$i]*$_SESSION['panier']['prix'][$i];
    }
    return round($total,2);
}

//--------------------------------
function retirerProduitDuPanier($id_produit_a_supprimer)
{
    $position_produit = array_search($id_produit_a_supprimer, $_SESSION['panier']['id_produit']); // grâce à la fonction prédéfinie array_search(), on va chercher à quel indice se trouve le produit à supprimer dans la SESSION['panier']

    if($position_produit !== false) // si la variable $position_produit retourne une valeur différente de false, cela veut dire qu'un indice a bien été trouvé dans la session 'panier
    {
        // la fonction array_splice() permet de supprimer une ligne dans le tableau session, et elle remonte les indices inférieur du tableau aux indices supérierus du tableau, si je supprime un produit à l'indice 4, tous les prduits inférieur remonteront
        array_splice($_SESSION['panier']['titre'], $position_produit,1);
        array_splice($_SESSION['panier']['id_produit'], $position_produit,1);
        array_splice($_SESSION['panier']['quantite'], $position_produit,1);
        array_splice($_SESSION['panier']['prix'], $position_produit,1);
    }
}
?>