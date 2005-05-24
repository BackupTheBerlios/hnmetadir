<?PHP

include('HEADER.php');

// ##################################################################


$tpl->set_file('FileRef','admin-branches.html');

// - Création d'un branche
// -------------------------------
if($action == 'ajouter') {

	if($_POST['nom']) {
		$nom = addslashes($_POST['nom']);
		$description = addslashes($_POST['description']);
		$admin = $_POST['admin'];

		$db->query('INSERT INTO `BRANCHES` (BRA_NOM,BRA_DESCRIPTION,BRA_ADMIN) VALUES ("'.$nom.'","'.$description.'","'.$admin.'")');
	}

	header('Location: admin-branches.php');

// - Suprrime une branche
// -------------------------------
} elseif($action == 'supprimer') {
	$db->query('DELETE FROM `BRANCHES` WHERE `BRA_ID`="'.$_GET['id'].'"');
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
	$db->query('SELECT * FROM `BRANCHES`');
	While( $data = $db->fetch_array() ) {
        	$tpl->set_var('b_id', $data['BRA_ID'] );
	        $tpl->set_var('b_nom', stripslashes($data['BRA_NOM']) );
		$tpl->parse('branches_block', 'branches', true);
	}

}

$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
