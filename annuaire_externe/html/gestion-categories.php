<?PHP

include('HEADER.php');

if( !$_SESSION['branche_admin'] ) header('Location: index.php');

// ##################################################################


$tpl->set_file('FileRef','gestion-categories.html');
 
switch($action) {

	case 'supprimer':
	func_supprimer();
	break;

	case 'ajouter':
	func_ajouter();

	default:
	func_index();
	break;

}


// - Listage des catégories
// ----------------------------------

function affstruct($pere,$espace,$block)
{
        global $db,$tpl;
        $query='SELECT * FROM `CATEGORIES` WHERE `CAT_PARENTID`="'.$pere.'" AND `BRANCHES_BRA_ID`="'.$_SESSION['branche_id'].'" ORDER BY `CAT_NOM` ASC';
	$result = mysql_query($query) or die(mysql_error());
        $n = mysql_num_rows($result);
	
	if($block == 'categories' && $pere == 0) {
		$espace .= '';
	} else {
		$espace .= '.&nbsp;&nbsp;&nbsp;&nbsp;';
	}
	
        for ($i=0; $i<$n; $i++)
	{
                $nom=mysql_result($result,$i,"CAT_NOM");
                $id=mysql_result($result,$i,"CAT_ID");
		$description=mysql_result($result,$i,"CAT_DESCRIPTION");

                $tpl->set_var('c_nom', $espace. stripslashes($nom) );
		$tpl->set_var('c_description', $description ); // laisser slashé 
                $tpl->set_var('c_id', $id );
                $tpl->parse($block.'_block', $block, true);
                affstruct($id,$espace,$block);
       }
}               

// - Affichage de la liste des categoriess
/// ------------------------------------------------

function func_index() 
{
	global $tpl,$db;
	$tpl->set_block('FileRef', 'categories', 'categories_block');
	affstruct(0,'','categories');

	// la listbox
	$tpl->set_block('FileRef', 'parents', 'parents_block');
	affstruct(0,'','parents');
}

function func_supprimer() 
{
	global $tpl,$db,$id;
	$db->query('DELETE FROM `CATEGORIES` WHERE `CAT_ID`="'.$id.'" AND `BRANCHES_BRA_ID`="'.$_SESSION['branche_id'].'"');

	header('Location: gestion-categories.php');
}

function func_ajouter() 
{
	global $tpl,$db,$_POST;
	
	if( $_POST['nom'] )
	{
		$nom = addslashes($_POST['nom']);
		$description = addslashes($_POST['description']);
		$db->query('INSERT INTO `CATEGORIES` (CAT_NOM, CAT_DESCRIPTION, CAT_PARENTID, BRANCHES_BRA_ID) VALUES ("'.$nom.'","'.$description.'","'.$_POST['parent'].'","'.$_SESSION['branche_id'].'")');
	}

	header('Location: gestion-categories.php');
}


$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
