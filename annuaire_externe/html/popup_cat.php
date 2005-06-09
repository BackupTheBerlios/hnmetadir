<?PHP

$popup = true;
include('HEADER.php');

// ##################################################################




// Ajout d'une catégorie
// -----------------------------

if($_GET['action'] == 'ajout') 
{
	if($_POST) 
	{
		$nom = addslashes($_POST['nom']);
		$description = $_POST['description'];
		$parentid = $_POST['parentid'];
		$db->query('INSERT INTO `CATEGORIES` (CAT_NOM,CAT_DESCRIPTION,CAT_PARENTID,CAT_DTCREA,CAT_COOPE) VALUES ("'.$nom.'","'.$description.'","'.$parentid.'","CURDATE()", "'.$_SESSION['auth_id'].'");');
		echo '<script language="javascript">
			window.opener.location.reload();
			window.close();
		      </script>';
	}

	// ---------------------
	?>

	<SCRIPT LANGUAGE="JavaScript">
		parent.window.resizeTo('350','200');
	</SCRIPT>
	<form action="popup_cat.php?action=ajout" method="POST">
	<input type="hidden" name="parentid" value="<?=$_GET['id']?>">
	<b>Nom :</b><br>
	<input type="text" maxlength="50" size="45" name="nom"><br>
	<b>Description :</b><br>
	<textarea cols="45" name="description"></textarea><br><br>
	<center><input type="image" src="templates/images/valide.gif"> <a href="#" onclick="window.close();"><img src="templates/images/del.gif" border="0"></a></center>
	</form>
	<?php
}



// Edition d'une catégorie
// ---------------------------------------

elseif($_GET['action'] == 'edit')
{
	if($_POST)
	{
                $nom = addslashes($_POST['nom']);
                $description = $_POST['description'];
                $id = $_POST['id'];

		$db->query('UPDATE `CATEGORIES` SET `CAT_NOM`="'.$nom.'", `CAT_DESCRIPTION`="'.$description.'", `CAT_COOPE`="'.$_SESSION['auth_id'].'", `CAT_DTMAJ`="CURDATE()" WHERE `CAT_ID`="'.$id.'"');

                echo '<script language="javascript">
                        window.opener.location.reload();
                        window.close();
                      </script>';

	}


	// --------------------

	$db->query('SELECT CAT_NOM,CAT_DESCRIPTION,CAT_ID FROM `CATEGORIES` WHERE CAT_ID="'.(int)$_GET['id'].'"');
	$row = $db->fetch_array();
	$nom = stripslashes($row['CAT_NOM']);
	$description = stripslashes($row['CAT_DESCRIPTION']);

        ?>

        <SCRIPT LANGUAGE="JavaScript">
                parent.window.resizeTo('350','200');
        </SCRIPT>
        <form action="popup_cat.php?action=edit" method="POST">
        <input type="hidden" name="id" value="<?=$row['CAT_ID']?>">
        <b>Nom :</b><br>
        <input type="text" maxlength="50" size="45" name="nom" value="<?=$nom?>"><br>
        <b>Description :</b><br>
        <textarea cols="45" name="description"><?=$description?></textarea><br><br>
        <center><input type="image" src="templates/images/valide.gif"> <a href="#" onclick="window.close();"><img src="templates/images/del.gif" border="0"></a></center>
        </form>
        <?php


}

// ######################################################################

include('FOOTER.php');
?>
