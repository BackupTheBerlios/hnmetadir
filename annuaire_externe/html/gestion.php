<?PHP

include('HEADER.php');

if( !$_SESSION['branche_admin'] ) header('Location: index.php');

// ##################################################################


$tpl->set_file('FileRef','gestion.html');

$id = (int)$_SESSION['branche_id'];

// - Formulaire posté !
// ---------------------------------

if( $_POST['nom'] && $_POST['description'] ) 
{
	$nom = addslashes( $_POST['nom'] );
	$description = addslashes( $_POST['description'] );

	$db->query('UPDATE `BRANCHES` SET `BRA_NOM`="'.$nom.'", `BRA_DESCRIPTION`="'.$description.'", `BRA_DTMAJ`=CURDATE(), `BRA_COOPE`="'.$_SESSION['auth_id'].'" WHERE `BRA_ID`="'.$id.'"');

}

// - Affichage de la page 
// ---------------------------------

$db->query('SELECT * FROM `BRANCHES` WHERE `BRA_ID`="'.$id.'"');
$row = $db->fetch_array();
$tpl->set_var('b_id', $row['BRA_ID'] );
$tpl->set_var('b_nom', stripslashes($row['BRA_NOM']) );
$tpl->set_var('b_description', stripslashes($row['BRA_DESCRIPTION']) );




$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
