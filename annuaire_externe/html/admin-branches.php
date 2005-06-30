<?PHP

include('HEADER.php');

// ##################################################################


$tpl->set_file('FileRef','admin-branches.html');

// - Création d'un branche
// -------------------------------
if($_GET['action'] == 'ajouter') {

	if($_POST['nom']) {
		$nom = addslashes($_POST['nom']);
		$description = addslashes($_POST['description']);
		$admin = $_POST['admin'];

		$db->query('INSERT INTO `CATEGORIES` (CAT_NOM,CAT_DESCRIPTION,CAT_ADMIN,CAT_PARENTID) VALUES ("'.$nom.'","'.$description.'","'.$admin.'","0")');
	}

	header('Location: admin-branches.php');

// - Suprrime une branche
// -------------------------------
} elseif($_GET['action'] == 'supprimer') {

	$tabcat=array();
	$where = get_subcats($_GET['id']);

	$db->query('DELETE FROM `CATEGORIES` WHERE `CAT_ID` IN '.$where);
	header('Location: admin-branches.php');

// - Listage, normal
// -------------------------------
} else {

	# affichage de la liste des users pour l'overflow 
	$tpl->set_block('FileRef', 'users', 'users_block');
	$db->query('SELECT `USE_ID`,`USE_NOM`,`USE_PRENOM` FROM `USERS`');
	While( $data = $db->fetch_array() ) {
        	$tpl->set_var('u_id', $data['USE_ID'] );
	        $tpl->set_var('u_nom', stripslashes($data['USE_NOM']) );
		$tpl->set_var('u_prenom', stripslashes($data['USE_PRENOM']) );
		$tpl->parse('users_block', 'users', true);
	}

	# affichage de la liste des branches 
	$tpl->set_block('FileRef', 'branches', 'branches_block');
	$db->query('SELECT * FROM `CATEGORIES` WHERE `CAT_PARENTID`="0" ORDER BY `CAT_NOM`');
	While( $data = $db->fetch_array() ) {
        	$tpl->set_var('b_id', $data['CAT_ID'] );
	        $tpl->set_var('b_nom', stripslashes($data['CAT_NOM']) );
		$tpl->parse('branches_block', 'branches', true);
	}

}

$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
