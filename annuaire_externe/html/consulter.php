<?PHP

include('HEADER.php');


// ##################################################################

$tpl->set_file('FileRef','consulter.html');


// - Fonction - Listage des catégories
// ------------------------------------

function affstruct_cat($pere,$espace,$bra_id)
{
        global $db,$tpl,$_GET;
        $query='SELECT `CAT_ID`,`CAT_NOM`,`CAT_DESCRIPTION` FROM `CATEGORIES` WHERE `CAT_PARENTID`="'.$pere.'" AND `BRANCHES_BRA_ID`="'.$bra_id.'" ORDER BY `CAT_NOM` ASC';
	$result = mysql_query($query) or die(mysql_error());
        $n = mysql_num_rows($result);
	$cats = explode('|', $_GET['cats']);
	$espace .= '<img src="templates/images/espace.gif" alt="espace">';
	
        for ($i=0; $i<$n; $i++)
	{
                $nom=stripslashes( mysql_result($result,$i,"CAT_NOM") );
                $id=mysql_result($result,$i,"CAT_ID");
		$description=mysql_result($result,$i,"CAT_DESCRIPTION");
		
              	$tpl->set_var('nom', '<a href="consulter.php?bra_id='.$bra_id.'&cat='.$id.'">'.$nom.'</a>' );
		$tpl->set_var('espace', $espace );
		$tpl->set_var('description', $description ); // laisser slashé 
		$tpl->set_var('icone', '<img src="templates/images/folder.png" alt="folder">' );
                $tpl->set_var('id', $id );
                $tpl->parse('arbre_block', 'arbre', true);

		if($_GET['cat'] && ($id != $_GET['cat']) )
		{
                	#affstruct_cat($_GET['cat'],$espace,$bra_id);
		}
       }
} 



// - Fonction - Listage des entitee d'une catégorie
// -------------------------------------------------

function affstruct_ent($cat,$pere,$espace)
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

                affstruct_ent($cat,$id,$espace);
       }
}              

// - Fonction - Listage des entitee d'une catégorie
// -------------------------------------------------

function affstruct_users($id)
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

// - Fonction - Listage des branches
// -------------------------------------------

function affstruct_branches() {
	global $db,$tpl,$_GET;

	$db->query('SELECT * FROM `BRANCHES` ORDER BY `BRA_ID` ASC');
	while( $data = $db->fetch_array() )
	{
		$braid = $data['BRA_ID'];
		$branom  = stripslashes($data['BRA_NOM']);

		$tpl->set_var('id', $braid );
		$tpl->set_var('nom', '<a href="?bra_id='.$braid.'">'.$branom.'</a>' );
		$tpl->set_var('icone', '<img src="templates/images/branche.png" alt="fiche">' );
		$tpl->set_var('espace', '' );
		$tpl->parse('arbre_block', 'arbre', true);

		 // branche à explorer ?
                 if( $braid == $_GET['bra_id'] ) {
                         affstruct_cat(0,'',$_GET['bra_id']);
                 }

	}
}

// ------------------------------------------------
// ------------------------------------------------

$tpl->set_block('FileRef', 'arbre', 'arbre_block');

/*
	affstruct_cat((int)$cat,'0','');
	$tpl->set_var('chemin', chemin('categorie',(int)$cat,'') );
*/	
	affstruct_branches();

$tpl->parse('FileOut', 'FileRef');

// ######################################################################
$tpl->parse('arbre_block', 'arbre', true);
include('FOOTER.php');
?>
