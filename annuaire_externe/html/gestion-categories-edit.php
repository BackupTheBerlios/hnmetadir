<?PHP

include('HEADER.php');

if( !$_SESSION['branche_admin'] ) header('Location: index.php');

// ##################################################################


$tpl->set_file('FileRef','gestion-categories-edit.html');

$id = (int)$_GET['id'];

// cette arbo existe bien ?
$db->query('SELECT * FROM `CATEGORIES` WHERE `CAT_ID`="'.$id.'"');
if(! $db->num_rows()) {
	header('Location: gestion-categories.php');
}


// - Traitement du du formulaire
// --------------------------------------


if($_POST['nom']) 
{
	echo 'Nbr lecture :'.count($_POST['users_r'])."\n";
	echo 'Nbr ecriture : '.count($_POST['users_w']);

	// on supprime toutes les perms pour toutes les refaires
	$db->query('DELETE FROM `PERMISSIONS` WHERE `CATEGORIES_CAT_ID`="'.$id.'"');

	// on reconstruit le droit de lecture
	for($i=0; $i<count($_POST['users_r']);$i++) 
	{
		mysql_query('INSERT INTO `PERMISSIONS` (CATEGORIES_CAT_ID,GROUPES_GRO_ID,PERM_TYPE) VALUES ("'.$id.'","'.$_POST['users_r'][$i].'","R")');
	}

	// on reconstruit le droit d'ajout
	for($i=0; $i<count($_POST['users_w']);$i++) 
	{
		$found = false;

		for($j=0; $j<count($_POST['users_r']);$j++) 
		{
			if($_POST['users_w'][$i] == $_POST['users_r'][$j] ) $found = true;
		}	
		echo '!';

		// Read & Write
		if( $found == true ) 
		{ 
			mysql_query('UPDATE `PERMISSIONS` SET `PERM_TYPE`="RW" WHERE CATEGORIES_CAT_ID="'.$id.'" AND `GROUPES_GRO_ID`="'.$_POST['users_w'][$i].'"');
		} 
		else 
		{ 
			mysql_query('INSERT INTO `PERMISSIONS` (CATEGORIES_CAT_ID,GROUPES_GRO_ID,PERM_TYPE) VALUES ("'.$id.'","'.$_POST['users_w'][$i].'","W")');
		}
	}

	$db->query('UPDATE `CATEGORIES` SET `CAT_NOM`="'.addslashes($_POST['nom']).'", `CAT_DESCRIPTION`="'.addslashes($_POST['description']).'" WHERE `CAT_ID`="'.$id.'"');	
	header('Location: gestion-categories.php');

}

// - Affichage du formulaire
// --------------------------------------

$row = $db->fetch_array();
$tpl->set_var('c_id', $row['CAT_ID'] );
$tpl->set_var('c_nom', stripslashes($row['CAT_NOM']) );
$tpl->set_var('c_description', stripslashes($row['CAT_DESCRIPTION']) );

# affichage des groupes
$tpl->set_block('FileRef', 'groupes', 'groupes_block');
$db->query('SELECT `GRO_ID`, `GRO_NOM` FROM `GROUPES`');
While( $data = $db->fetch_array() ) {
	$tpl->set_var('g_id', $data['GRO_ID'] );
	$tpl->set_var('g_nom', stripslashes($data['GRO_NOM']) );
	$tpl->parse('groupes_block', 'groupes', true);
}

# affichage des groupes autorisé en lecture
$tpl->set_block('FileRef', 'users_r', 'users_r_block');
$db->query('SELECT * FROM `PERMISSIONS`,`GROUPES` WHERE `PERM_TYPE` LIKE "R%" AND `CATEGORIES_CAT_ID`="'.$id.'" AND `GROUPES_GRO_ID`=`GRO_ID` ');
While( $data = $db->fetch_array() ) {
	$tpl->set_var('ur_id', $data['GRO_ID'] );
	$tpl->set_var('ur_nom', stripslashes($data['GRO_NOM']) );
	$tpl->parse('users_r_block', 'users_r', true);
}

# affichage des groupes autorisé en ajout
$tpl->set_block('FileRef', 'users_w', 'users_w_block');
$db->query('SELECT * FROM `PERMISSIONS`,`GROUPES` WHERE `PERM_TYPE` LIKE "%W" AND `CATEGORIES_CAT_ID`="'.$id.'" AND `GROUPES_GRO_ID`=`GRO_ID` ');
While( $data = $db->fetch_array() ) {
	$tpl->set_var('uw_id', $data['GRO_ID'] );
	$tpl->set_var('uw_nom', stripslashes($data['GRO_NOM']) );
	$tpl->parse('users_w_block', 'users_w', true);
}


$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
