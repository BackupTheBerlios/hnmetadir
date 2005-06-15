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


// ORGANISATION DU CODE
// ~~~~~~~~~~~~~~~~~~~~
// fonction d'affichage des catégories
// fonction d'affichage des entitées
// fonction de listage des personnes
//
// Si $cat est définie alors :
//    on recupere les id parents
//    on affiche le menu et la description dans le cadre haut droit
// Sinon si $ent est défini
//    on récupere les id des entitées parents
//    on affiche le calques des personnes
//    on affiche les personnes
//    on affiche les propriétés/valeur de l'entitée
// Fin de boucle
// On affiche les catégories en appelant la "fonction cat" qui appelera la "fonction ent" si besoin
//



// - Fonction - Listage des catégories
// ------------------------------------

function affstruct_cat($pere,$espace)
{
        global $db,$tpl,$_GETi,$tabcat;

        $query='SELECT `CAT_ID`,`CAT_NOM`,`CAT_DESCRIPTION` FROM `CATEGORIES` WHERE `CAT_PARENTID`="'.$pere.'"  AND `CAT_ID`!="0" ORDER BY `CAT_NOM` ASC';
	$result = mysql_query($query) or die(mysql_error());
        $n = mysql_num_rows($result);
	$cats = explode('|', $_GET['cats']);
	if($pere != 0) {
		$espace .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|';
	} else {
		$espace .= '|';
	}
	
        for ($i=0; $i<$n; $i++)
	{
                $nom=stripslashes( mysql_result($result,$i,"CAT_NOM") );
                $id=mysql_result($result,$i,"CAT_ID");
		$description=mysql_result($result,$i,"CAT_DESCRIPTION");
		
              	$tpl->set_var('nom', '<a href="consulter.php?cat='.$id.'">'.$nom.'</a>' );
		$tpl->set_var('espace', $espace.'&nbsp;--' );
		$tpl->set_var('description', $description ); // laisser slashé 
                $tpl->set_var('id', $id );

		// MENU
		// --------------------------------
		$menu = "
			  <b>Catégorie :</b><br>
			  - <a href=\"#\" onclick=\"window.open(\'popup_cat.php?action=ajout&id=".$id."\', \'Ajouter une sous catégorie\', config=\'height=100, width=100, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no\');\">Ajouter</a><br>
			  - <a href=\"#\" onclick=\"window.open(\'popup_cat.php?action=edit&id=".$id."\', \'Editer cette catégorie\', config=\'height=100, width=100, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no\');\">Editer</a><br>
			  <b>Entitee :</b><br>
			  - <a  href=\"#\" onclick=\"window.open(\'popup_ent.php?action=ajout&cat_parent=".$id."\', \'Ajouter entitée\', config=\'height=600, width=600, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Ajouter</a><br>
			  <b>Gestion :</b><br>
			  - <a href=\"#\" onclick=\"window.open(\'popup_droits.php?id=".$id."\', \'Gestion des droits\', config=\'height=600, width=660, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Les droits</a><br>
			  - <a htef=\"\">Les champs spéciaux</a><br";
			  
		$menu = str_replace("\"", "&quot;", $menu);
		$menu = str_replace("\t", "", $menu);
		$menu = str_replace("\n", "", $menu);

		// -----------------------------------

		if($pere == 0) {
			$tpl->set_var('icone', '<img src="templates/images/branche.png" alt="folder">' );
			$tpl->set_var('menu', $menu);
		} else {
			$tpl->set_var('icone', '<img src="templates/images/folder.png" alt="folder">' );
			$tpl->set_var('menu', $menu);
		}

                $tpl->parse('arbre_block', 'arbre', true);


		// on regarde si c'est un des id du chemin dans lequel on doit s'enfoncer
		$found = false;
		for($j=0;$j<count($tabcat);$j++)
		{
			if($id == $tabcat[$j]) $found = true;
		}

		if($found == true) 
		{
			affstruct_cat($id,$espace);

			// affiche les entitées
			affstruct_ent($id,0,$espace);
		}
       }
} 



// - Fonction - Listage des entitee d'une catégorie
// -------------------------------------------------

function affstruct_ent($cat,$pere,$espace)
{
        global $db,$tpl,$tabent;

        $query='SELECT `ENT_ID`,`ENT_NOMINATION`,`ENT_RAISONSOCIAL` FROM `ENTITEES` WHERE `ENT_PARENTID`="'.$pere.'" AND `CATEGORIES_CAT_ID`="'.$cat.'" ORDER BY `ENT_NOMINATION` ASC';
        $result = mysql_query($query) or die(mysql_error());
        $n = mysql_num_rows($result);
        $espace .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|';
        for ($i=0; $i<$n; $i++)
        {
				$nom = stripslashes( mysql_result($result,$i,"ENT_RAISONSOCIAL") );
				$nom .= ' '.stripslashes( mysql_result($result,$i,"ENT_NOMINATION") );
				$id  = mysql_result($result,$i,"ENT_ID");

               	$tpl->set_var('nom', '<a href="consulter.php?ent='.$id.'">'.$nom.'</a>' );
		$tpl->set_var('espace', $espace.'&nbsp;--&nbsp;' );
                $tpl->set_var('icone', '<img src="templates/images/entity.png" alt="entitee">' );
                $tpl->set_var('id', $id );

		// menu
		$menu = "- <a href=\"#\" onclick=\"window.open(\'popup_ent.php?action=edition&id=".$id."\', \'Edition\', config=\'height=600, width=660, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Editer cette entitée</a><br>
			- Ajouter une sous-entitée<br>
			- Ajouter une personne";
                $menu = str_replace("\"", "&quot;", $menu);
                $menu = str_replace("\t", "", $menu);
                $menu = str_replace("\n", "", $menu);
		$tpl->set_var('menu', $menu);

                $tpl->parse('arbre_block', 'arbre', true);

		
                $found = false;
                for($j=0;$j<count($tabent);$j++)
                {
                        if($id == $tabent[$j]) $found = true;
                }

                if($found == true)
                {
                	affstruct_ent($cat,$id,$espace,$bra_id);
                }

       }
}              


// - Fonction - Listage des personnes d'une entitée 
// -------------------------------------------------

function aff_personnes($id)
{
	global $tpl,$db;

	$db->query('SELECT * FROM `PERSONNES`,`AFFECTE_ENTITEES_PERSONNES` WHERE AFFECTE_ENTITEES_PERSONNES.ENTITEES_ENT_ID="'.$id.'" AND AFFECTE_ENTITEES_PERSONNES.PERSONNES_PER_ID=PERSONNES.PER_ID');
	$tpl->set_block('FileRef', 'personnes', 'personnes_block');

	while( $data = $db->fetch_array($req) )
	{
		$tpl->set_var('u_id', $data['PER_ID'] );
		$nom = $data['PER_TITRE'].' '.stripslashes($data['PER_NOM']).' '.stripslashes($data['PER_PRENOM']);
		$nom = '<a href="#" onclick="window.open(\'popup_personne.php?id='.$id.'\', \'Fiche de '.addslashes($nom).'\', config=\'height=400, width=600, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');">'.$nom.'</a>';
		$tpl->set_var('p_nom',  $nom);
		$tpl->set_var('p_mail', $data['PER_MAIL'] );
		$tpl->set_var('p_tel', $data['PER_TEL'] );
		$tpl->set_var('p_mobile', $data['PER_MOBILE']);
		$tpl->set_var('p_fonction', $data['PER_FONCTION']);

		$tpl->parse('personnes_block', 'personnes', true);
	}
}

// ------------------------------------------------
// ------------------------------------------------

	$tabcat = array();
	$tabent = array();

	// ON NAVIGUE DANS LES CATEGORIE
	if($_GET['cat']) 
	{
		// fonction permettant de récuperer tout les id des cats parents
		$tabcat = chemin_categorie($_GET['cat']);
		// on cache la partie "personne"
		$tpl->set_var('div_pdisp', 'none');

		// On affiche la description de la cat
		$db->query('SELECT `CAT_DESCRIPTION` FROM `CATEGORIES` WHERE `CAT_ID`="'.(int)$_GET['cat'].'"');
		$data = $db->fetch_array();

		$contenu  = stripslashes($data['CAT_DESCRIPTION']);
		$tpl->set_var('contenu', $contenu);
	} 
	// ON AFFICHE UNE ENTITEE ET SES SOUS ENTITEE
	elseif($_GET['ent'])
	{
		// on récupere les ids des entitées parentes
		$tabent = chemin_entitee($_GET['ent']);
		// on affiche le calque des personnes
		$tpl->set_var('div_pdisp', 'display');
		// hop on affiche la liste des personnes
		aff_personnes((int)$_GET['ent']);

		// on affiche les infos sur l'entitée


		$sql='SELECT * FROM ENTITEES WHERE ENT_ID="'.$_GET['ent'].'"';
		$CIL=InitPOReq($sql,'annuaire_externe');
		$rep=$db->query($sql);
		$data=$db->fetch_array();
		$tmp = '<table>';

		foreach ($CIL as $pobj) 
		{
			$CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
			$NmChamp = $pobj->NmChamp;

			// consultation ou édition ?
			// a modifier suivant profil
			$CIL[$NmChamp]->TypEdit = 'C';
			if ($CIL[$NmChamp]->TypeAff!="HID" && ($CIL[$NmChamp]->TypEdit!="C" || $CIL[$NmChamp]->ValChp!="") ) 
			{ 
			  	$tmp .= '<tr><td><b>'.$CIL[$NmChamp]->Libelle.'</b>';
				if ($CIL[$NmChamp]->TypEdit!="C" && $CIL[$NmChamp]->Comment!="") 
				{
					$tmp .= echspan("legendes9px","<BR>".$CIL[$NmChamp]->Comment);
				} 

				$tmp .= '</td>'."\n";
				$tmp .= '<td><b>:</b> ';
			  	// traitement valeurs avant MAJ
				$CIL[$NmChamp]->DirEcho = false;
		  	  	$CIL[$NmChamp]->InitAvMaj($_SESSION['auth_id']);
				$tmp .= $CIL[$NmChamp]->EchoEditAll(); // pas de champs hidden
				$tmp .= '</td></tr>'."\n";
			}
		}

		$tmp .= '</table>';

		$tpl->set_var('contenu', $tmp);
		
	}
	else
	{
		$tpl->set_var('div_pdisp', 'none');
	}

	$tpl->set_block('FileRef', 'arbre', 'arbre_block');
	affstruct_cat(0, '');

$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
