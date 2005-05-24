<?PHP

include('HEADER.php');


// ##################################################################

$tpl->set_file('FileRef','consulter.html');


// - Fonction - Listage des catégories
// ------------------------------------

function affstruct_arbre($pere,$espace)
{
        global $db,$tpl;
        $query='SELECT `CAT_ID`,`CAT_NOM`,`CAT_DESCRIPTION` FROM `CATEGORIES` WHERE `CAT_PARENTID`="'.$pere.'" AND `BRANCHES_BRA_ID`="'.$_SESSION['branche_id'].'" ORDER BY `CAT_NOM` ASC';
	$result = mysql_query($query) or die(mysql_error());
        $n = mysql_num_rows($result);
	$espace .= '<img src="templates/images/espace.gif" alt="espace">';
        for ($i=0; $i<$n; $i++)
	{
                $nom=stripslashes( mysql_result($result,$i,"CAT_NOM") );
                $id=mysql_result($result,$i,"CAT_ID");
		$description=mysql_result($result,$i,"CAT_DESCRIPTION");
		
		// catégorie déployable ou non
		$req = mysql_query('SELECT `ENT_ID` FROM `ENTITEES` WHERE `CATEGORIES_CAT_ID`="'.$id.'"');
		if( mysql_num_rows($req) ) {
                	$tpl->set_var('nom', '<a href="consulter.php?cat='.$id.'">'.$nom.'</a>' );
		} else {
			$tpl->set_var('nom', $nom );
		}
		$tpl->set_var('espace', $espace );
		$tpl->set_var('description', $description ); // laisser slashé 
		$tpl->set_var('icone', '<img src="templates/images/folder.png" alt="folder">' );
                $tpl->set_var('id', $id );
                $tpl->parse('arbre_block', 'arbre', true);

                affstruct_arbre($id,$espace);
       }
} 



// - Fonction - Listage des entitee d'une catégorie
// -------------------------------------------------

function affstruct_cat($cat,$pere,$espace)
{
        global $db,$tpl;
        $query='SELECT `ENT_ID`,`ENT_NOMINATION`,`ENT_RAISONSOCIAL` FROM `ENTITEES` WHERE `ENT_PARENTID`="'.$pere.'" AND `CATEGORIES_CAT_ID`="'.$cat.'" ORDER BY `ENT_NOMINATION` ASC';
        $result = mysql_query($query) or die(mysql_error());
        $n = mysql_num_rows($result);
        $espace .= '<img src="templates/images/espace.gif" alt="espace">';
        for ($i=0; $i<$n; $i++)
        {
		$nom         = stripslashes( mysql_result($result,$i,"ENT_RAISONSOCIAL") );
		$nom	     .= ' '.stripslashes( mysql_result($result,$i,"ENT_NOMINATION") );
		$id          = mysql_result($result,$i,"ENT_ID");
		$liendetail  = '<a href="#" onclick="window.open(\'entitee.php?id='.$id.'\', \'Fiche de {nom}\', config=\'height=600, width=400, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no\');"><small>[détails]</small></a>';

		// entité déployable ou non
		$req = mysql_query('SELECT * FROM `AFFECTE_ENTITEES_FICHES` WHERE `ENTITEES_ENT_ID`="'.$id.'"') or die(mysql_error()); 
		if( mysql_num_rows($req) ) {
                	$tpl->set_var('nom', '<a href="consulter.php?ent='.$id.'">'.$nom.'</a> '.$liendetail );
		} else {
			$tpl->set_var('nom', $nom.' '.$liendetail );
		}
                $tpl->set_var('espace', $espace );
                $tpl->set_var('icone', '<img src="templates/images/entity.png" alt="entitee">' );
                $tpl->set_var('id', $id );
                $tpl->parse('arbre_block', 'arbre', true);

                affstruct_cat($cat,$id,$espace);
       }
}              

// - Fonction - Listage des entitee d'une catégorie
// -------------------------------------------------

function affstruct_entitee($id)
{
	global $tpl,$db;

	$db->query('SELECT * FROM `FICHES`,`AFFECTE_ENTITEES_FICHES` WHERE AFFECTE_ENTITEES_FICHES.ENTITEES_ENT_ID="'.$id.'" AND AFFECTE_ENTITEES_FICHES.FICHES_FIC_ID=FICHES.FIC_ID');
	while( $data = $db->fetch_array($req) )
	{
		$tpl->set_var('id', $data['FIC_ID'] );
		$nom = stripslashes($data['FIC_NOM']).' '.stripslashes($data['FIC_PRENOM']);
		$nom = '<a href="#" onclick="window.open(\'fiche.php?id='.$id.'\', \'Fiche de {nom}\', config=\'height=600, width=400, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no\');">'.$nom.'</a>';
		$tpl->set_var('nom',  $nom);
		$tpl->set_var('icone', '<img src="templates/images/user.png" alt="fiche">' );
		$tpl->parse('arbre_block', 'arbre', true);
	}
}

// ------------------------------------------------
// ------------------------------------------------

$tpl->set_block('FileRef', 'arbre', 'arbre_block');

if( $cat )
{
	// on est dans une catégorie
	affstruct_cat((int)$cat,'0','');
	$tpl->set_var('chemin', chemin('categorie',(int)$cat,'') );
}
elseif( $ent )
{
	// on affiche la liste des fiches d'une entitée
	affstruct_entitee($ent);
	$tpl->set_var('chemin', chemin('entitee',(int)$ent,'') );
}
elseif( $bra ) {
{
        #affstruct_bra(0,'');
}
else
{
	affstruct_arbre(0,'');
}

$tpl->parse('FileOut', 'FileRef');

// ######################################################################
$tpl->parse('arbre_block', 'arbre', true);
include('FOOTER.php');
?>
