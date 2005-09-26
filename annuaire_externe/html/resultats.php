<?PHP

include('HEADER.php');

// ##################################################################


$tpl->set_file('FileRef','resultats.html');

// - Fonction permetant de rÃ©cupÃ©rer/reconstituer le chemin Ã  partir de l'id d'une catÃ©gorie ou d'une entitÃ©e
// -- Arguments :
// --- type   : 'entite' ou 'categorie'
// --- id     : l'id de l'objet en court
// --- chemin : Ne vaut rien par dÃ©faut

function chemin($type,$id,$chemin)
{
	global $chemin;
	
	if( $type == 'entite' ) 
	{
		$query='SELECT `ENT_ID`,`ENT_RAISONSOCIAL`,`ENT_NOMINATION`,`ENT_PARENTID`,`CATEGORIES_CAT_ID` FROM `ENTITES` WHERE `ENT_ID`="'.$id.'"';
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
			chemin('entite',$parentid,$chemin);
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
			$chemin = '<img src="templates/images/branche.png" alt=""> <a href="consulter.php?cat='.$id.'">'.$nom.'</a> '.$chemin;

		} 
		else 
		{
			$chemin = '<b>></b> <img src="templates/images/folder.png" alt=""> <a href="consulter.php?cat='.$id.'">'.$nom.'</a> '.$chemin;
			chemin('categorie', $parentid, $chemin);
		}
	}
	return $chemin;
}

// DEBUT ----------------------------------------------------------------------------------------------


if( $_GET['type'] == 'avancee' )
{
        $condexists=false;
        $TAB_VARS = $_POST;

        foreach ( $TAB_VARS as $NmVar=>$ValVar) {
                if (substr($NmVar, 0, 3)=="tf_") { // au moins une var de filtre existe
                        // reconstitution nom de la var du Type Requête
                        $NomChp=substr($NmVar,3);
                        $nmvarTR="tf_".$NomChp; // type de filtre
                        $nmvarVR="rq_".$NomChp; // Valeur de la Requete
                        $nmvarNEG="neg_".$NomChp; // Negation
                        $cond = SetCond ($_POST[$nmvarTR],$_POST[$nmvarVR],$_POST[$nmvarNEG],$NomChp);
                        if ($cond!="") {
                                $condexists=true;
                                if ($new_wh!="") $new_wh.=" AND ";
                                $new_wh.=$cond;
                        }
                } // sin si variable de filtre
        } // fin boucle sur les var POSTEES


        if( $_GET['recherche'] == 'entites' )
        {
                $db->query('SELECT `ENT_ID` FROM `ENTITES` WHERE '.$new_wh);
                $tpl->set_block('FileRef', 'entites', 'entites_block');
                
                // nb de résultats
                $tpl->set_var('e_resultats', $db->num_rows() );
                while( $data = $db->fetch_array() ) {
                        $chemin ='';
                        $tpl->set_var('line', chemin('entite', $data['ENT_ID'], '') );
                        $tpl->parse('entites_block', 'entites', true);
                }

                $tpl->set_block('FileRef', 'categories', 'categories_block');
                $tpl->set_var('c_resultats', 0 );
                $tpl->set_block('FileRef', 'personnes', 'personnes_block');
                $tpl->set_var('p_resultats', 0 );

        } elseif( $_GET['recherche'] == 'personnes' ) {
                
                $db->query('SELECT `PER_ID`,`PER_TITRE`,`PER_NOM`,`PER_PRENOM`,`PER_VILLE` FROM `PERSONNES` WHERE '.$new_wh);
                $tpl->set_block('FileRef', 'personnes', 'personnes_block');

                // nb de résultats
                $tpl->set_var('p_resultats', $db->num_rows() );
	
                while( $data = $db->fetch_array() ) 
                {
                        $per_id  = $data['PER_ID'];
                        $nom = $data['PER_TITRE'].' '.stripslashes($data['PER_NOM'].' '.$data['PER_PRENOM']);
                        
                        // une personne peut etre dans plusieurs entités
                        $res2 = mysql_query('SELECT `ENTITES_ENT_ID` FROM `AFFECTE_ENTITES_PERSONNES` WHERE `PERSONNES_PER_ID`="'.$per_id.'"');
                        
                        while( $data2 = mysql_fetch_array($res2) )
                        {
                            unset($chemin);
                            $chemin = chemin('entite', $data2['ENTITES_ENT_ID'], '').' > ';
                            $tpl->set_var('line', $chemin.'<img src="templates/images/user.png" alt="personne"> <a href="#" onclick="window.open(\'popup_pers.php?action=consultation&per_id='.$per_id.'&ent_id='.$data2['ENTITES_ENT_ID'].'\', \'\', config=\'height=400, width=600, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');">'.$nom.'</a>' );
                            $tpl->parse('personnes_block', 'personnes', true);
                            $i++;
                        }
                        // nb de résultats
                        $tpl->set_var('p_resultats', $i );
                }


                $tpl->set_block('FileRef', 'categories', 'categories_block');
                $tpl->set_var('c_resultats', 0 );
                $tpl->set_block('FileRef', 'entites', 'entites_block');
                $tpl->set_var('e_resultats', 0 );

        }

}
else
{	
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
	
	
	// - Recherche dans les entites 
	// ---------------------------------------------------------
	
	$tpl->set_block('FileRef', 'entites', 'entites_block');
	$db->query('SELECT `ENT_ID` FROM `ENTITES` WHERE ENT_RAISONSOCIAL LIKE "%'.$_POST['entree'].'%" || ENT_NOMINATION LIKE "%'.$_POST['entree'].'%" || ENT_SIRET LIKE "%'.$_POST['entree'].'%" || ENT_CONAF LIKE "%'.$_POST['entree'].'%" || ENT_ADRESSE LIKE "%'.$_POST['entree'].'%" || ENT_ADRESSE_COMP LIKE "%'.$_POST['entree'].'%" || ENT_VILLE LIKE "%'.$_POST['entree'].'%" || ENT_CODEPOSTAL LIKE "%'.$_POST['entree'].'%" || ENT_SITEWEB LIKE "%'.$_POST['entree'].'%" || ENT_MOTCLES LIKE "%'.$_POST['entree'].'%" ORDER BY `ENT_ID` ASC');
	
	// nb de résultats
	$tpl->set_var('e_resultats', $db->num_rows() );
	
	while( $data = $db->fetch_array() )
	{
		$chemin ='';
		$tpl->set_var('line', chemin('entite', $data['ENT_ID'], '') );
		$tpl->parse('entites_block', 'entites', true);
	}
	
	
	// - Recherche dans les personnes 
	// ---------------------------------------------------------
	
	$tpl->set_block('FileRef', 'personnes', 'personnes_block');
	$db->query('SELECT `PER_ID`,`PER_PRENOM`,`PER_NOM`,`PER_VILLE`,`PER_TITRE` FROM `PERSONNES` WHERE `PER_NOM` LIKE "%'.$_POST['entree'].'%" || `PER_PRENOM` LIKE "%'.$_POST['entree'].'%" ||`PER_VILLE` LIKE "%'.$_POST['entree'].'%" ORDER BY `PER_NOM` ASC');
	
        while( $data = $db->fetch_array() ) 
        {
            $per_id  = $data['PER_ID'];
            $nom = $data['PER_TITRE'].' '.stripslashes($data['PER_NOM'].' '.$data['PER_PRENOM']);

            // une personne peut etre dans plusieurs entités
            $res2 = mysql_query('SELECT `ENTITES_ENT_ID` FROM `AFFECTE_ENTITES_PERSONNES` WHERE `PERSONNES_PER_ID`="'.$per_id.'"');

            while( $data2 = mysql_fetch_array($res2) )
            {
                unset($chemin);
                $chemin = chemin('entite', $data2['ENTITES_ENT_ID'], '').' > ';
                $tpl->set_var('line', $chemin.'<img src="templates/images/user.png" alt="personne"> <a href="#" onclick="window.open(\'popup_pers.php?action=consultation&per_id='.$per_id.'&ent_id='.$data2['ENTITES_ENT_ID'].'\', \'\', config=\'height=400, width=600, toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no\');">'.$nom.'</a>' );
                $tpl->parse('personnes_block', 'personnes', true);
                $i++;
            }
            // nb de résultats
            $tpl->set_var('p_resultats', $i );
        }
}
	

$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
