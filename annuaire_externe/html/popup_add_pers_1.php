<?PHP

$popup = true;
include('HEADER.php');

// ##################################################################


?>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="templates/style.css">
  </head>
  <body>

<?php

# L'user cherche si le contacte existe ?
if($_POST) 
{
        ?>
        <SCRIPT LANGUAGE="JavaScript">parent.window.resizeTo('400','400');</SCRIPT>
        <h2>Résultats</h2>
        <img src="templates/images/info.jpeg"> Cliquez sur un des résultats pour ajouter une casquette à une personne, sinon <a href="popup_add_pers_2.php?action=ajout">Cliquez ici</a> pour créer une nouvelle personne.</b><br>
        <br>
        <?php

        $db->query('SELECT `PER_ID`, `PER_NOM`, `PER_PRENOM`, `PER_VILLE` FROM `PERSONNES` WHERE `PER_NOM` LIKE "%'.$_POST['nom'].'%" ORDER BY `PER_NOM` ASC');
        if($db->num_rows())
        {
                echo '<ul>';
                while( $data = $db->fetch_array() )
                {
                        $nom = stripslashes($data['PER_NOM']).' '.stripslashes($data['PER_PRENOM']).' - '.stripslashes($data['PER_VILLE']);
                        echo "<li><a href=\"popup_add_pers_2.php?action=ajout&per_id=".$data['PER_ID']."\">$nom</a></li>\n";
                }
                echo '</ul>';
        } 
        else
        {
                echo '<i>Aucun résultat</i>';
        }

}
else
{
        $_SESSION['ent_id'] = $_GET['ent_id'];
        ?>

        <SCRIPT LANGUAGE="JavaScript">parent.window.resizeTo('400','200');</SCRIPT>
        <h2>Recherche</h2>
        Cette étape va vous permettre de savoir si le contact existe déjà dans l'annuaire. Cependant, vous pouvez ajouter directement une <a href="popup_add_pers_2.php?action=ajout">nouvelle personne</a>.<br><br>
        <form method="POST">
          <center><b>Nom :</b> <input type="text" name="nom"> <input type="image" src="templates/images/valide.gif"></center>
        </form>
        <?php
}

echo '  </body></html>';

// ######################################################################

include('FOOTER.php');
?>
