<?PHP

include('HEADER.php');

// ##################################################################


$tpl->set_file('FileRef','admin-branches-edit.html');


$db->query('SELECT * FROM `BRANCHES` WHERE `BRA_ID`="'.(int)$_GET['id'].'"');

// il y a t'il bien une branche sur cette id ?
if( $db->num_rows() ) 
{
	// formulaire valid�
	if( $_POST ) 
	{
		$nom = addslashes($_POST['nom']);
		$description = addslashes($_POST['description']);
		$admin = $_POST['admin'];
		$db->query('UPDATE `BRANCHES` SET `BRA_NOM`="'.$nom.'", `BRA_DESCRIPTION`="'.$description.'", `BRA_ADMIN`="'.$admin.'" WHERE BRA_ID="'.(int)$_GET['id'].'"');
		header('Location: admin-branches.php');
		
	} 
	else 
	{
		$row = $db->fetch_array();
		$tpl->set_var('id', $row['BRA_ID'] );
		$tpl->set_var('nom', stripslashes($row['BRA_NOM']) );
		$tpl->set_var('description', stripslashes($row['BRA_DESCRIPTION']) );
		
		# affichage de la liste des users pour l'overflow 
		$tpl->set_block('FileRef', 'users', 'users_block');
		$db->query('SELECT `USE_ID`,`USE_NOM`,`USE_PRENOM` FROM `USERS`');

		While( $data = $db->fetch_array() ) 
		{
       			$tpl->set_var('u_id', $data['USE_ID'] );
        		$tpl->set_var('u_nom', stripslashes($data['USE_NOM']) );
			$tpl->set_var('u_prenom', stripslashes($data['USE_PRENOM']) );

			// si le bon user, hop selected
			if($row['BRA_ADMIN'] == $data['USE_ID']) {
				$tpl->set_var('selected', 'selected' );
			} else {
				$tpl->set_var('selected', '');
			}

			$tpl->parse('users_block', 'users', true);
		}
	}
}
else
{
	header('Location: admin-branches.php');
}

$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
