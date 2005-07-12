<?PHP

include('HEADER.php');

// ##################################################################


$tpl->set_file('FileRef','resultats.html');

// - Fonction permetant de rÃ©cupÃ©rer/reconstituer le chemin Ã  partir de l'id d'une catÃ©gorie ou d'une entitÃ©e
// -- Arguments :
// --- type   : 'entitee' ou 'categorie'
// --- id     : l'id de l'objet en court
// --- chemin : Ne vaut rien par dÃ©faut

function chemin($type,$id,$chemin)
{
	global $chemin;
	
	if( $type == 'entitee' ) 
	{
		$query='SELECT `ENT_ID`,`ENT_RAISONSOCIAL`,`ENT_NOMINATION`,`ENT_PARENTID`,`CATEGORIES_CAT_ID` FROM `ENTITEES` WHERE `ENT_ID`="'.$id.'"';
		$result = mysql_query($query) or die(mysql_error());
        $row = mysql_fetch_array($result);

		$nom = stripslashes( $row['ENT_RAISONSOCIAL'].' '.$row['ENT_NOMINATION'] );
		$parentid = $row['ENT_PARENTID'];
		$chemin = '> <img src="templates/images/entity.png" alt=""> <a href="consulter.php?ent='.$id.'">'.$nom.'</a> '.$chemin;
			
		if( $parentid == 0 )
		{
			chemin('categorie', $row['CATEGORIES_CAT_ID'], $chemin);
		} 
		else 
		{
			chemin('entitee',$parentid,$chemin);
		}
	}
	
	if( $type == 'categorie' )
	{
		$query='SELECT `CAT_ID`,`CAT_NOM`,`CAT_PARENTID` FROM `CATEGORIES` WHERE `CAT_ID`="'.$id.'"';
		$result = mysql_query($query) or die(mysql_error());
        $row = mysql_fetch_array($result);

		$id = $row['CAT_ID'];
		$nom = stripslashes( $row['CAT_NOM'] );
		$parentid = $row['CAT_PARENTID'];
			
		if( $parentid == 0 )
		{
			$chemin = '<img src="templates/images/branche.png" alt=""> <a href="consulter.php?id='.$id.'">'.$nom.'</a> '.$chemin;

		} 
		else 
		{
			$chemin = '<b>></b> <img src="templates/images/folder.png" alt=""> <a href="consulter.php?id='.$id.'">'.$nom.'</a> '.$chemin;
			chemin('categorie', $parentid, $chemin);
		}
	}
	return $chemin;
}

// - Recherche dans les catégories
// ---------------------------------------------------------

$tpl->set_block('FileRef', 'categories', 'categories_block');
$db->query('SELECT `CAT_ID` FROM `CATEGORIES` WHERE `CAT_NOM` LIKE "%'.$_POST['entree'].'%" ORDER BY `CAT_ID` ASC');

// nb de résultats
$tpl->set_var('c_resultats', $db->num_rows() );

while( $data = $db->fetch_array() ) 
{
	$chemin ='';
	$tpl->set_var('line', chemin('categorie', $data['CAT_ID'], '') );
	$tpl->parse('categories_block', 'categories', true);
}


// - Recherche dans les entitees 
// ---------------------------------------------------------

$tpl->set_block('FileRef', 'entitees', 'entitees_block');
$db->query('SELECT `ENT_ID` FROM `ENTITEES` WHERE ENT_RAISONSOCIAL LIKE "%'.$_POST['entree'].'%" || ENT_NOMINATION LIKE "%'.$_POST['entree'].'%" || ENT_SIRET LIKE "%'.$_POST['entree'].'%" || ENT_CONAF LIKE "%'.$_POST['entree'].'%" || ENT_ADRESSE LIKE "%'.$_POST['entree'].'%" || ENT_ADRESSE_COMP LIKE "%'.$_POST['entree'].'%" || ENT_VILLE LIKE "%'.$_POST['entree'].'%" || ENT_CODEPOSTAL LIKE "%'.$_POST['entree'].'%" || ENT_SITEWEB LIKE "%'.$_POST['entree'].'%" || ENT_MOTCLES LIKE "%'.$_POST['entree'].'%" ORDER BY `ENT_ID` ASC');

// nb de résultats
$tpl->set_var('e_resultats', $db->num_rows() );

while( $data = $db->fetch_array() )
{
	$chemin ='';
        $tpl->set_var('line', chemin('entitee', $data['ENT_ID'], '') );
        $tpl->parse('entitees_block', 'entitees', true);
}


// - Recherche dans les personnes 
// ---------------------------------------------------------

$tpl->set_block('FileRef', 'personnes', 'personnes_block');
$db->query('SELECT `PER_ID`,`PER_PRENOM`,`PER_NOM`,`PER_VILLE`,`PER_TITRE` FROM `PERSONNES` WHERE `PER_NOM` LIKE "%'.$_POST['entree'].'%" || `PER_PRENOM` LIKE "%'.$_POST['entree'].'%" ||`PER_VILLE` LIKE "%'.$_POST['entree'].'%" ORDER BY `PER_NOM` ASC');

// nb de résultats
$tpl->set_var('p_resultats', $db->num_rows() );

while( $data = $db->fetch_array() )
{
	$id  = $data['PER_ID'];
	$nom = $data['PER_TITRE'].' '.stripslashes($data['PER_NOM'].' '.$data['PER_PRENOM']).' - '.$data['PER_VILLE'];
        $tpl->set_var('line', '<img src="templates/images/user.png" alt="personne"> <a href="#" onclick="window.open(\'popup_personne.php?id='.$id.'\', \'Fiche de '.addslashes($nom).'\', config=\'height=400, width=600, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');">'.$nom.'</a>' );
        $tpl->parse('personnes_block', 'personnes', true);
}


//////////////////////////////////////////
$tpl->set_var('entree', $_POST['entree'] );


$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
