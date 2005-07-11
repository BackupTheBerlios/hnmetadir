<?PHP

include('HEADER.php');


// ##################################################################


$tpl->set_file('FileRef','consulter.html');


// EXPLICATION
// ~~~~~~~~~~~
// On initialise en premier deux tableaux $tabcat et tabent. Ces deux tableaux sont destin� � recevoir
// l'ensemble des ids des cat�gories et des entit�es qui seront "utilis�" lorsqu'une personne s'enfonce
// dans l'arborescence.
// Donc via les deux fonctions qui se trouvent dans functions.php, on y insert ces ids.
// On lance l'affichage de la branche via affstruc_branche().
// Si la variable $cat est d�j� d�finie, cela signifie que l'user est d�j� ou veut s'enfoncer dans une
// des arborescences (note: arborescence = branche + cat�forie + entit�e)
// On est donc a pr�sent dans la boucle qui affiche les branche, hop ca nous envoie vers l'affichage des
// cat�gorie. Ici, on liste l'ensemble des cat�gories tout en v�rifiant que l'id de celle ci n'est pas
// pr�sente dans le $tabcat. Si c'est le cas, cela signifie qu'il faut s'enfoncer dans cette cat�gorie.
// Parallelement on liste les entit�es que poss�de cette cat�gorie.


// ORGANISATION DU CODE
// ~~~~~~~~~~~~~~~~~~~~
// fonction d'affichage des cat�gories
// fonction d'affichage des entit�es
// fonction de listage des personnes
//
// Si $cat est d�finie alors :
//    on recupere les id parents
//    on affiche le menu et la description dans le cadre haut droit
// Sinon si $ent est d�fini
//    on r�cupere les id des entit�es parents
//    on affiche le calques des personnes
//    on affiche les personnes
//    on affiche les propri�t�s/valeur de l'entit�e
// Fin de boucle
// On affiche les cat�gories en appelant la "fonction cat" qui appelera la "fonction ent" si besoin
//



// - Fonction - Listage des cat�gories
// ------------------------------------

function affstruct_cat($pere,$espace)
{
	global $db,$tpl,$_GET,$tabcat,$user;

	$query='SELECT `CAT_ID`,`CAT_NOM`,`CAT_DESCRIPTION`,`CAT_ADMIN` FROM `CATEGORIES` WHERE `CAT_PARENTID`="'.$pere.'"  AND `CAT_ID`!="0" ORDER BY `CAT_NOM` ASC';
	$result = $db->query($query) or die(mysql_error());
	$n = $db->num_rows($result);
	
	if($pere != 0) {
		$espace .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|';
	}
	
	for ($i=0; $i<$n; $i++)
	{
		$nom=stripslashes( mysql_result($result,$i,"CAT_NOM") );
		$id=mysql_result($result,$i,"CAT_ID");
		$description=mysql_result($result,$i,"CAT_DESCRIPTION");
                $cat_admin=mysql_result($result,$i,"CAT_ADMIN");
		
		
		$tpl->set_var('nom', '<a href="consulter.php?cat='.$id.'">'.$nom.'</a>' );
		
		if($pere == 0) { 
			$tpl->set_var('espace', '' );
		} else {
			$tpl->set_var('espace', $espace.'&nbsp;--');
		}
		
		$tpl->set_var('description', $description ); // laisser slash� 
		$tpl->set_var('id', $id );
		
                // on r�cup�re les droits
		$droit_w = $user->HaveAccess($id, 'W');
		$droit_a = $user->HaveAccess($id, 'A');
		


		// MENU
		// --------------------------------

		$menu = '';

		if( $droit_w == true || $droit_a == true ) 
		{
			$menu =  "<b>Cat�gorie</b><br>
			- <a href=\"javascript:void(0);\" onclick=\"window.open(\'popup_cat.php?action=ajout&cat_parentid=".$id."\', \'\', config=\'height=100, width=100, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no\');\">Ajouter</a><br>
			- <a href=\"javascript:void(0);\" onclick=\"window.open(\'popup_cat.php?action=edit&id=".$id."\', \'\', config=\'height=100, width=100, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no\');\">Editer</a><br>
			- <a href=\"consulter.php?action=supprimer&cat_id=".$id."\" onclick=\"choix=confirm(\'Etes vous sur de vouloir supprimer cette cat�gorie ?\nToutes les sous cat�gories et les entit�es qui y sont reli�es seront supprim�es !\'); if(choix==false){return false;}\">Supprimer</a><br>
			<b>Entit�e :</b><br>
			- <a  href=\"javascript:void(0);\" onclick=\"window.open(\'popup_ent.php?action=ajout&cat_parentid=".$id."\', \'\', config=\'height=600, width=600, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Ajouter</a><br>
			<b>Gestion :</b><br>";
		}

		if( $droit_a == true )
		{
			$menu .= "- <a href=\"javascript:void(0);\" onclick=\"window.open(\'popup_droits.php?id=".$id."\', \'\', config=\'height=600, width=660, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Les droits</a><br>
			- <a htef=\"#\">Les champs sp�ciaux</a><br>";
		}
			$menu .= "<b>Extraction :</b><br>
			- <a href=\"javascript:void(0);\" onclick=\"window.open(\'extractions.php?type=entitees&cat_id=".$id."\', \'\', config=\'height=100, width=100, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Entit�es seulement</a><br>
			- <a href=\"javascript:void(0);\" onclick=\"window.open(\'extractions.php?type=categories&cat_id=".$id."\', \'\', config=\'height=100, width=100, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Toutes les Cat�gories</a><br>";

		$menu = str_replace("\"", "&quot;", $menu);
		$menu = str_replace("\t", "", $menu);
		$menu = str_replace("\n", "", $menu);
		$menu = str_replace("\r", "", $menu); 
		
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

			// affiche les entit�es
			affstruct_ent($id,0,$espace);
		}
       }
} 



// - Fonction - Listage des entitee d'une cat�gorie
// -------------------------------------------------

function affstruct_ent($cat,$pere,$espace)
{
        global $db,$tpl,$tabent,$user;

        $query='SELECT `ENT_ID`,`ENT_NOMINATION`,`ENT_RAISONSOCIAL` FROM `ENTITEES` WHERE `ENT_PARENTID`="'.$pere.'" AND `CATEGORIES_CAT_ID`="'.$cat.'" ORDER BY `ENT_NOMINATION` ASC';
        $result = $db->query($query) or die(mysql_error());
        $n = $db->num_rows($result);
        $espace .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|';
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
                // ----------------------------------------------

                $menu = '';
		$droit_w = $user->HaveAccess($cat, 'W');
		$droit_a = $user->HaveAccess($cat, 'A');

                if( $droit_w == true || $droit_a == true ) 
                {
		      $menu = "- <a href=\"#\" onclick=\"window.open(\'popup_ent.php?action=edition&id=".$id."\', \'\', config=\'height=600, width=660, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Editer cette entit�e</a><br>
			- <a href=\"consulter.php?action=supprimer&ent_id=".$id."\" onclick=\"choix=confirm(\'Etes vous sur de vouloir supprimer cette entit�e ?\nToutes les sous-entit�es qui y sont reli�es seront supprim�es !\'); if(choix==false){return false;}\">Supprimer cette entit�e</a><br>
			- <a href=\"javascript:void(0);\" onclick=\"window.open(\'popup_ent.php?action=ajout&cat_parentid=".$cat."&ent_parentid=".$id."\', \'\', config=\'height=600, width=660, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Ajouter une sous-entit�e</a><br>
			- <a href=\"javascript:void(0);\" onclick=\"window.open(\'popup_add_pers_1.php?ent_id=".$id."\', \'\', config=\'height=600, width=660, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Ajouter une personne</a><br>";
                }

			$menu .= "- <a href=\"javascript:void(0);\" onclick=\"window.open(\'extractions.php?type=personnes&ent_id=".$id."\', \'\', config=\'height=100, width=100, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Extraire les personnes</a><br>
                        - <a href=\"javascript:void(0);\" onclick=\"window.open(\'popup_ent.php?action=consultation&id=".$id."\', \'\', config=\'height=600, width=660, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Version imprimable</a><br>";

                $menu = str_replace("\"", "&quot;", $menu);
                $menu = str_replace("\t", "", $menu);
                $menu = str_replace("\n", "", $menu);
		$tpl->set_var('menu', $menu);
                // ----------------------------------------------

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


// - Fonction - Listage des personnes d'une entit�e 
// -------------------------------------------------

function aff_personnes($ent_id,$droit_w,$droit_a)
{
	global $tpl,$db;

	$db->query('SELECT * FROM `PERSONNES`,`AFFECTE_ENTITEES_PERSONNES` WHERE AFFECTE_ENTITEES_PERSONNES.ENTITEES_ENT_ID="'.$ent_id.'" AND AFFECTE_ENTITEES_PERSONNES.PERSONNES_PER_ID=PERSONNES.PER_ID');
	$tpl->set_block('FileRef', 'personnes', 'personnes_block');

	while( $data = $db->fetch_array($req) )
	{
                $per_id = $data['PER_ID'];
		if($droit_a == 'true' || $droit_w == 'true')
		{
                	$menu = '';
                	$menu = "- <a href=\"javascript:void(0);\" onclick=\"window.open(\'popup_pers.php?per_id=".$per_id."&ent_id=".$ent_id."\', \'\', config=\'height=100, width=100, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');\">Editer</a><br>
                	- <a href=\"javascript:void(0);\" onclick=\"choix=confirm(\'Etes vous sur de vouloir supprimer cette entit�e ?\nToutes les sous-entit�es qui y sont reli�es seront supprim�es !\'); if(choix==true) { window.open(\'popup_pers.php?action=supprimer&per_id=".$per_id."&ent_id=".$ent_id."\', \'\', config=\'height=100, width=100, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');}\">Supprimer</a><br>";
		} else {
			$menu = 'Aucun droit';
		}

                $menu = str_replace("\"", "&quot;", $menu);
                $menu = str_replace("\t", "", $menu);
                $menu = str_replace("\n", "", $menu);

		$tpl->set_var('u_id', $data['PER_ID'] );
		$nom = $data['PER_TITRE'].' '.stripslashes($data['PER_NOM']).' '.stripslashes($data['PER_PRENOM']);
		$nom = '<a href="#" onclick="window.open(\'popup_pers.php?action=consultation&per_id='.$per_id.'&ent_id='.$ent_id.'\', \'Fiche de '.addslashes($nom).'\', config=\'height=400, width=600, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');">'.$nom.'</a>';
		$tpl->set_var('p_nom',  $nom);
		$tpl->set_var('p_menu',  $menu);
                $tpl->set_var('p_mail', $data['AEP_EMAIL'] );
		$tpl->set_var('p_tel', $data['AEP_TEL'] );
		$tpl->set_var('p_mobile', $data['AEP_MOBILE']);
		$tpl->set_var('p_fonction', $data['AEP_FONCTION']);

		$tpl->parse('personnes_block', 'personnes', true);
	}
}

// ------------------------------------------------
// ------------------------------------------------

	// suppression -------------------------------
	if( $_GET['action'] == 'supprimer')
	{
                if( $_GET['cat_id']  )
                {
                       $tabcat=array();
	               $where = get_subcats($_GET['cat_id']);
	               $db->query('DELETE FROM `CATEGORIES` WHERE `CAT_ID` IN '.$where);
                }
                elseif( $_GET['ent_id'] )
                {
                       $tabent=array();
	               $where = get_subents($_GET['ent_id']);
	               $db->query('DELETE FROM `ENTITEES` WHERE `ENT_ID` IN '.$where);
                }	
	}
	// -------------------------------------------


	$tabcat = array();
	$tabent = array();

	// ON NAVIGUE DANS LES CATEGORIE
	if($_GET['cat']) 
	{
		// fonction permettant de r�cuperer tout les id des cats parents
		$tabcat = chemin_categorie($_GET['cat']);
		// on cache la partie "personne"
		$tpl->set_var('div_pdisp', 'none');

		// On affiche la description de la cat
		$db->query('SELECT `CAT_DESCRIPTION` FROM `CATEGORIES` WHERE `CAT_ID`="'.(int)$_GET['cat'].'"');
		$data = $db->fetch_array();

		$contenu   = '<h2>Description</h2>'."\n";
		$contenu  .= stripslashes($data['CAT_DESCRIPTION']);
		$tpl->set_var('contenu', $contenu);
	} 
	// ON AFFICHE UNE ENTITEE ET SES SOUS ENTITEE
	elseif($_GET['ent'])
	{

                $sql='SELECT * FROM ENTITEES WHERE ENT_ID="'.$_GET['ent'].'"';
                $rep=$db->query($sql);
                $data=$db->fetch_array();

	
		// on r�cupere les ids des entit�es parentes
		$tabent = chemin_entitee($_GET['ent']);
		// on affiche le calque des personnes
		$tpl->set_var('div_pdisp', 'display');
		
		// hop on affiche la liste des personnes
                $droit_w = $user->HaveAccess($data['CATEGORIES_CAT_ID'], 'W');
		$droit_a = $user->HaveAccess($data['CATEGORIES_CAT_ID'], 'A');
		aff_personnes((int)$_GET['ent'], $droit_w, $droit_a);


		// on envoie le menu
		$onglets   = '<ul id="tabnav">';
    		$onglets  .= '<li id="li_entitee" class="active"><a href="#" onclick="javascript:ShowTab(\'entitee\');">Entit�e</a></li>';
        	$onglets  .= '<li id="li_personnes" class=""><a href="#" onclick="javascript:ShowTab(\'personnes\');">Personnes</a></li>';
		$onglets  .= '</ul><br>';
		$tpl->set_var('onglets', $onglets);
		

		// on affiche les infos sur l'entit�e
		$CIL=InitPOReq($sql,'annuaire_externe');
		$tmp = '<table>';
		
                // l'user a t'il acc�s en lecture pour les champs sp�ciaux
                $access = $user->HaveAccess($data['CATEGORIES_CAT_ID'], 'R');

		//$vtb_name=RecupLib("CATEGORIES","CAT_ID","CAT_VTBNAME",$_GET['ent']);
		$vtb_name=RecupLib("CATEGORIES","CAT_ID","CAT_VTBNAME",205);
		
		foreach ($CIL as $pobj) 
		{
			$NmChamp = $pobj->NmChamp;
						
/*			if ($vtb_name && strstr($NmChamp,"PROPRIETE")) 
                        {
                                //$CIL[$NmChamp]->NmTable=$_vtb_name;
                                $pobj->NmTable=$vtb_name;
                                //$pobj->NmBase="annuaire_externe";
                                //$pobj->NmChamp=$NmChamp;
					
                                $pobj->InitPO();
				echo 'botte ';
			}
*/			
			
			$CIL[$NmChamp]->ValChp=$data[$NmChamp];
			$CIL[$NmChamp]->TypEdit = 'C';

                        // on affiche aps les champs cach�
			if ($CIL[$NmChamp]->Typaff_l!='' && $CIL[$NmChamp]->TypeAff!="HID" && ($CIL[$NmChamp]->TypEdit!="C" || $CIL[$NmChamp]->ValChp!="") ) 
			{
				// on vire les champs qui ne doivent pas etre affich� (droit ou inutiles)
                                $display = true;
				if($NmChamp == 'ENT_PARENTID' || $NmChamp == 'CATEGORIES_CAT_ID') $display = false;
                                if( ereg('PROPRIETE', $NmChamp) && !$access) $display = false;

                                        
                                if( $display == true ) 
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
                                        $tmp .= $CIL[$NmChamp]->EchoEditAll(); // pas de champs hidden
                                        $tmp .= '</td></tr>'."\n";
				}
			}
		}

		$tmp .= '</table>';

		$tpl->set_var('contenu', $tmp);
		
	}

	$tpl->set_block('FileRef', 'arbre', 'arbre_block');
	affstruct_cat(0, '');

$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
