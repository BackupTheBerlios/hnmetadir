<?PHP

include('HEADER.php');
$tpl->set_file('FileRef','admin-groupes-edit.html');

// ##################################################################
// ##################################################################
// ##################################################################


// - Formulaire validé 
// ---------------------------------- 
if($_POST['nom']) {

	# mise a jour du nom
	$db->query('UPDATE `GROUPES` SET `GRO_NOM`="'.addslashes($_POST['nom']).'" WHERE `GRO_ID`="'.(int)$_GET['id'].'"');	
	# on vide la liste d'affectation
	$db->query('DELETE FROM `AFFECTE_USERS_GROUPES` WHERE `GROUPES_GRO_ID`="'.(int)$_GET['id'].'"'); 

	# mise a jour des affectations
	for($i=0; $i<count($_POST['usersgroup']);$i++) {
		$db->query('INSERT INTO `AFFECTE_USERS_GROUPES` (GROUPES_GRO_ID,USERS_USE_ID) VALUES ("'.(int)$_GET['id'].'","'.(int)$_POST['usersgroup'][$i].'")');	
	}

	header('Location: admin-groupes.php');
}

// - Affichage des valeur dans le formulaire 
// ----------------------------------------- 

$db->query('SELECT `GRO_NOM` FROM `GROUPES` WHERE `GRO_ID`="'.(int)$_GET['id'].'"');

# si l'id est vide ou que l'id ne correspond a rien
if(! $db->num_rows()) {
	header('Location: admin-groupes.php');
}

$row = $db->fetch_array();
$tpl->set_var('nom', stripslashes($row['GRO_NOM']) );
$tpl->set_var('id', (int)$_GET['id'] );

# on récupere la liste des users
$tpl->set_block('FileRef', 'users', 'users_block');
$db->query('SELECT `USE_ID`,`USE_NOM`,`USE_PRENOM` FROM `USERS`');
While( $data = $db->fetch_array() ) {
	$tpl->set_var('u_id', $data['USE_ID'] );
	$tpl->set_var('u_nom', stripslashes($data['USE_NOM']) );
	$tpl->set_var('u_prenom', stripslashes($data['USE_PRENOM']) );
	$tpl->parse('users_block', 'users', true);
}

# on récupère la liste des personnes déjà affectées au groupe
$tpl->set_block('FileRef', 'usersgroup', 'usersgroup_block');
$db->query('SELECT * FROM `USERS`,`AFFECTE_USERS_GROUPES` WHERE AFFECTE_USERS_GROUPES.GROUPES_GRO_ID="'.(int)$_GET['id'].'" AND AFFECTE_USERS_GROUPES.USERS_USE_ID=USERS.USE_ID');
While( $data = $db->fetch_array() ) {
        $tpl->set_var('ug_id', $data['USE_ID'] );
	$tpl->set_var('ug_nom', stripslashes($data['USE_NOM']) );
        $tpl->set_var('ug_prenom', stripslashes($data['USE_PRENOM']) );
        $tpl->parse('usersgroup_block', 'usersgroup', true);
}




// ###################################################################
// ###################################################################
// ###################################################################

$tpl->parse('FileOut', 'FileRef');

include('FOOTER.php');
?>
