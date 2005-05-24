<?PHP

include('HEADER.php');


// ##################################################################

$tpl->set_file('FileRef','consulter.html');


// EXPLICATION
// ~~~~~~~~~~~
// On initialise en premier deux tableaux $tabcat et tabent. Ces deux tableaux sont destinés à recevoir
// l'ensemble des ids des catégories et des entitées qui seront "utilisé" lorsqu'une personne s'enfonce
// dans l'arborescence.
// Donc via les deux fonctions qui se trouvent dans functions.php, on y insert ces ids.
// On lance l'affichage de la branche via affstruc_branche().
// Si la variable $cat est déjà définie, cela signifie que l'user est déjà ou veut s'enfoncer dans une
// des arborescences (note: arborescence = branche + catéforie + entitée)
// On est donc a présent dans la boucle qui affiche les branche, hop ca nous envoie vers l'affichage des
// catégorie. Ici, on liste l'ensemble des catégories tout en vérifiant que l'id de celle ci n'est pas
// présente dans le $tabcat. Si c'est le cas, cela signifie qu'il faut s'enfoncer dans cette catégorie.
// Parallelement on liste les entitées que possède cette catégorie.


// - Fonction - Listage des catégories
// ------------------------------------

function affstruct_cat($pere,$espace,$bra_id)
{
        global $db,$tpl,$_GETi,$tabcat;

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


		// on regarde si c'est un des id du chemin dans lequel on doit s'enfoncer
		$found = false;
		for($j=0;$j<count($tabcat);$j++)
		{
			if($id == $tabcat[$j]) $found = true;
		}

		if($found == true) 
		{
			affstruct_cat($id,$espace,$bra_id);

			// affiche les entitées
			affstruct_ent($id,0,$espace,$bra_id);
		}
       }
} 



// - Fonction - Listage des entitee d'une catégorie
// -------------------------------------------------

function affstruct_ent($cat,$pere,$espace,$bra_id)
{
        global $db,$tpl,$tabent;

        $query='SELECT `ENT_ID`,`ENT_NOMINATION`,`ENT_RAISONSOCIAL` FROM `ENTITEES` WHERE `ENT_PARENTID`="'.$pere.'" AND `CATEGORIES_CAT_ID`="'.$cat.'" ORDER BY `ENT_NOMINATION` ASC';
        $result = mysql_query($query) or die(mysql_error());
        $n = mysql_num_rows($result);
        $espace .= '<img src="templates/images/espace.gif" alt="espace">';
        for ($i=0; $i<$n; $i++)
        {
		$nom         = stripslashes( mysql_result($result,$i,"ENT_RAISONSOCIAL") );
		$nom	     .= ' '.stripslashes( mysql_result($result,$i,"ENT_NOMINATION") );
		$id          = mysql_result($result,$i,"ENT_ID");

               	$tpl->set_var('nom', '<a href="consulter.php?bra_id='.$bra_id.'&ent='.$id.'">'.$nom.'</a>' );
                $tpl->set_var('espace', $espace );
                $tpl->set_var('icone', '<img src="templates/images/entity.png" alt="entitee">' );
                $tpl->set_var('id', $id );
                $tpl->parse('arbre_block', 'arbre', true);

		
                $found = false;
                for($j=0;$j<count($tabent);$j++)
                {
                        if($id == $tabent[$j]) $found = true;
                }

                if($found == true)
                {
			echo $id;
                	affstruct_ent($cat,$id,$espace,$bra_id);
                }

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
	
	$tabcat = array();
	$tabent = array();
	
	if($_GET['cat']) 
	{
		$tabcat = chemin_categorie($_GET['cat']);
	} 
	elseif($_GET['ent'])
	{
		$tabent = chemin_entitee($_GET['ent']);
	}

	affstruct_branches();

$tpl->parse('FileOut', 'FileRef');

// ######################################################################
$tpl->parse('arbre_block', 'arbre', true);
include('FOOTER.php');
?>
