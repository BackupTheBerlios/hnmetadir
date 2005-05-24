<?PHP

include('HEADER.php');
$tpl->set_file('FileRef','admin-groupes.html');

// ##################################################################
// ##################################################################
// ##################################################################


// - Ajout d'un groupe
// ---------------------------------- 
if($_GET['action'] == 'add' && $_POST['nom']) {
	$db->query('INSERT INTO `GROUPES` (GRO_NOM) VALUES ("'.addslashes($_POST['nom']).'")');	

}

// - Listage des groupes
// ----------------------------------- 

$tpl->set_block('FileRef', 'groupes', 'groupes_block');
$db->query('SELECT `GRO_ID`,`GRO_NOM` FROM `GROUPES`');

While( $data = $db->fetch_array() ) {
	$tpl->set_var('g_id', $data['GRO_ID'] );
	$tpl->set_var('g_nom', stripslashes($data['GRO_NOM']) );
	$tpl->parse('groupes_block', 'groupes', true);
}


// ###################################################################
// ###################################################################
// ###################################################################

$tpl->parse('FileOut', 'FileRef');

include('FOOTER.php');
?>
