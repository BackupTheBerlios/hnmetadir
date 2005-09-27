<?PHP

$popup = true;
include('HEADER.php');

// ##################################################################

function traitement_personne($requete) {

	// on affiche la premiere ligne avec les noms des champs
	$NM_TABLE = 'PERSONNES';
	$db->query('SELECT `NM_CHAMP` from '.$TBDname.' where NM_TABLE="'.$NM_TABLE.'" AND NM_CHAMP!="'.$NmChDT.'" AND ( `NM_CHAMP`="PER_IDLDAP" OR `NM_CHAMP`="PER_TITRE" OR `NM_CHAMP`="PER_NOM" OR `NM_CHAMP`="PER_PRENOM" OR `NM_CHAMP`="PER_ADRESSE" OR `NM_CHAMP`="PER_ADRESSE2" OR `NM_CHAMP`="PER_CODEPOSTALE" OR `NM_CHAMP`="PER_VILLE" OR `NM_CHAMP`="PER_PAYS" OR `NM_CHAMP`="PER_REGION" OR `NM_CHAMP`="PER_DATENAISS" OR `NM_CHAMP`="PER_SITEPERSO" ) ORDER BY ORDAFF, LIBELLE');

	while ( $CcChp = $db->fetch_array() )  {
		$NM_CHAMP=$CcChp[0];
		$ECT[$NM_CHAMP]=new PYAobj();
		$ECT[$NM_CHAMP]->NmBase=$DBName;
		$ECT[$NM_CHAMP]->NmTable=$NM_TABLE;
		$ECT[$NM_CHAMP]->NmChamp=$NM_CHAMP;
		$ECT[$NM_CHAMP]->TypEdit='C';
		$ECT[$NM_CHAMP]->InitPO();
	}

	$i = 0;
	foreach ($ECT as $PYAObj) {
		if( $i == 0 ) {
		echo '"'.$PYAObj->Libelle.'"';
		} else {
		echo ',"'.$PYAObj->Libelle.'"';
		}
		$i++;
	}
	unset($ECT);

	// on affiche les nom des champs spécifique
	$NM_TABLE = 'AFFECTE_ENTITES_PERSONNES';
	$db->query('SELECT `NM_CHAMP` FROM `DESC_TABLES` WHERE `NM_TABLE`="'.$NM_TABLE.'" AND `NM_CHAMP`!="TABLE0COMM" AND (`NM_CHAMP`="AEP_FONCTION" OR `NM_CHAMP`="AEP_TEL" OR `NM_CHAMP`="AEP_FAX" OR `NM_CHAMP`="AEP_MOBILE" OR `NM_CHAMP`="AEP_ABREGE" OR `NM_CHAMP`="AEP_EMAIL" OR `NM_CHAMP`="AEP_PRIVATECOMMENT") ORDER BY `ORDAFF`');

	while ( $CcChp = $db->fetch_array() )  
	{
		$NM_CHAMP=$CcChp[0];    
		$ECT[$NM_CHAMP] = new PYAobj();
		$ECT[$NM_CHAMP]->NmBase=$DBName; 
		$ECT[$NM_CHAMP]->NmTable=$NM_TABLE;
		$ECT[$NM_CHAMP]->NmChamp=$NM_CHAMP;
		$ECT[$NM_CHAMP]->TypEdit='C';   
		$ECT[$NM_CHAMP]->InitPO();
	}

	foreach ($ECT as $PYAObj) {
		echo ',"'.$PYAObj->Libelle.'"';
	}

	echo ',"Raison Social","Société"'."\r\n";
	// fin de l'affichage de la premiere ligne

	// on récupère la catégorie parent avant tout pour connaitre les droits
	$db->query('SELECT `CATEGORIES_CAT_ID` FROM `ENTITES` WHERE `ENT_ID`="'.$ent_id.'" LIMIT 1');
	$row = $db->fetch_array();

	$access = $user->HaveAccess($row['CATEGORIES_CAT_ID'], 'R');
	if($access == 'false') $access = $user->HaveAccess($row['CATEGORIES_CAT_ID'], 'W');
	if($access == 'false') $access = $user->HaveAccess($row['CATEGORIES_CAT_ID'], 'A');

	// !!!!!!!!!!!!!!!!!!!!!!!!!!! LA REQUETE ICI !!!!!!!!!!!!!!!!!!!!!!!!!
	$sql = 'SELECT `PER_IDLDAP`,`PER_TITRE`,`PER_NOM`,`PER_PRENOM`,`PER_ADRESSE`,`PER_ADRESSE2`,`PER_CODEPOSTALE`,`PER_VILLE`, `PER_PAYS`,`PER_REGION`,`PER_DATENAISS`,`PER_SITEPERSO`,`AEP_FONCTION`, `AEP_TEL`, `AEP_ABREGE`,`AEP_FAX`,`AEP_MOBILE`,`AEP_EMAIL`,`AEP_PRIVATECOMMENT`, ENTITES.ENT_RAISONSOCIAL, ENTITES.ENT_NOMINATION FROM `PERSONNES`, `AFFECTE_ENTITES_PERSONNES`, `ENTITES` WHERE `PERSONNES_PER_ID`=`PER_ID` AND `ENTITES_ENT_ID`="'.$ent_id.'" AND ENTITES.ENT_ID="'.$ent_id.'"';

	$CIL=InitPOReq($sql,$DBName);
	$rep=$db->query($sql);


	if( $db->num_rows() )
	{
		while( $data = $db->fetch_array() )
		{
			$j=0;
			foreach ($CIL as $pobj) 
			{ 
				$NmChamp = $pobj->NmChamp;
				$CIL[$NmChamp]->ValChp=$data[$NmChamp];
				$CIL[$NmChamp]->TypEdit = 'C';

				if( $j != 0 ) echo ',';
				if( $NmChamp == 'AEP_PRIVATECOMMENT' ) {
					if( $access == true )
					{
						$tmp = str_replace('"', '\"', stripslashes($CIL[$NmChamp]->ValChp) );
						echo '"'.$tmp.'"';
					} else {
						echo '""';
					}
				} else {
					$tmp = str_replace('"', '\"', stripslashes($CIL[$NmChamp]->ValChp) );
					echo '"'.$tmp.'"';
				}
				$j++;

			} // fin du foreach
			echo "\r\n";
		} // fin du while
	} // fin du mysql_num_rows()

}


/******************************************************************
*
* EXTRACTIONS DE TOUTES LES CATEGORIES  
*
*******************************************************************/

if( $_GET['type'] == 'categories' )
{
	$cat_id = (int)$_GET['cat_id'];
	$db->query('SELECT `CAT_NOM` FROM `CATEGORIES` WHERE `CAT_ID`="'.$cat_id.'" LIMIT 1');
	$row = $db->fetch_array();

	$filename = strtolower( $row['CAT_NOM'] );
	$filename = str_replace(' ', '_', $filename);

        header("Content-disposition: attachment; filename=extraction-personnes-$filename.csv");
        header("Content-Type: application/force-download");
        header("Content-Transfer-Encoding: binary");
	
	// on récupere les ids des sous cat en dessous
        function GetCatsForCSV($pere, $position)
        {
		global $z;
		
                $query='SELECT `CAT_ID`,`CAT_NOM`,`CAT_DESCRIPTION` FROM `CATEGORIES` WHERE `CAT_PARENTID`="'.$pere.'"  AND `CAT_ID`!="0" ORDER BY `CAT_NOM` ASC';
                $result = mysql_query($query) or die(mysql_error());
                $n = mysql_num_rows($result);

		if( $z != 0 )
		{
                   $position .= "\" \",";
                }
		$z++;


                for ($i=0; $i<$n; $i++)
                {
                        $nom = '"'.stripslashes(mysql_result($result,$i,"CAT_NOM")).'";';
			echo $position.$nom."\r\n";
                        $id  = mysql_result($result,$i,"CAT_ID");
                        GetCatsForCSV($id, $position);

                }
        }

        GetCatsForCSV($cat_id, $position);

}
/******************************************************************
*
* EXTRACTIONS DE TOUTES LES ENTITES D'UNE CATEGORIE 
*
*******************************************************************/

elseif( $_GET['type'] == 'entites' )
{

        // on genere le nom
        $cat_id = (int)$_GET['cat_id'];
        $db->query('SELECT `CAT_NOM` FROM `CATEGORIES` WHERE `CAT_ID`="'.$cat_id.'" LIMIT 1');
        $row = $db->fetch_array();

        $filename = strtolower( $row['CAT_NOM'] );
        $filename = str_replace(' ', '_', $filename);

        header("Content-disposition: attachment; filename=\"extraction-entites-$filename.csv\"");
        header("Content-Type: application/force-download");
        header("Content-Transfer-Encoding: binary");
        // fin de la generation du nom

        // on affiche la premiere ligne avec les noms des champs
        $NM_TABLE = 'ENTITES';
        $db->query("SELECT `NM_CHAMP` from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' ORDER BY ORDAFF, LIBELLE");
        $vtb_name = RecupLib("CATEGORIES","CAT_ID","CAT_VTBNAME",$cat_id);

        while ( $CcChp = $db->fetch_array() )  {
                $NM_CHAMP=$CcChp[0];
                $ECT[$NM_CHAMP]=new PYAobj();
                $ECT[$NM_CHAMP]->NmBase=$DBName;
                $ECT[$NM_CHAMP]->NmTable=$NM_TABLE;
                $ECT[$NM_CHAMP]->NmChamp=$NM_CHAMP;
                $ECT[$NM_CHAMP]->TypEdit='C';
                $ECT[$NM_CHAMP]->InitPO();
        }

        $i = 0;
        foreach ($ECT as $PYAObj) 
        {
                if ($PYAObj->TypeAff == "AUT" )
                {
                        if( $i != 0 ) echo ',';
                        if( $PYAObj->NmChamp == 'ENT_PROPRIETE1' || $PYAObj->NmChamp == 'ENT_PROPRIETE2' || $PYAObj->NmChamp == 'ENT_PROPRIETE3' || $PYAObj->NmChamp == 'ENT_PROPRIETE4' || $PYAObj->NmChamp == 'ENT_PROPRIETE5')
                        {
			     $ECT[$PYAObj->NmChamp]->NmTable=$vtb_name;
			     $ECT[$PYAObj->NmChamp]->InitPO();
			     echo '"'.$ECT[$PYAObj->NmChamp]->Libelle.'"';
                        }
                        else
                        {
                                echo '"'.$PYAObj->Libelle.'"';
                                $i++;
                        }
                }
        }

        // comme on affiche toute les entités de toutes les sous catégories qui se trouve en dessous de l'id
        // les libellés des champs spéciaux ne seront pas pareille car il risque d'y avoir des sous catégories debutant
        // sur des champs spéciaux differents
        echo "\r\n";
        // fin de l'affichage de la premiere ligne

        // on rempli le tableau
        $tabcat = array();
	$where = get_subcats($cat_id);

        $sql = 'SELECT * FROM `ENTITES` WHERE `CATEGORIES_CAT_ID` IN '.$where;
	$CIL=InitPOReq($sql,$DBName);
	$rep=$db->query($sql);


        if( $db->num_rows() )
        {
                while( $data = $db->fetch_array() )
                {
                        $j=0;

                        $access = $user->HaveAccess($data['CATEGORIES_CAT_ID'], 'R');
                        if($access == 'false') $access = $user->HaveAccess($data['CATEGORIES_CAT_ID'], 'W');
                        if($access == 'false') $access = $user->HaveAccess($data['CATEGORIES_CAT_ID'], 'A');

        		foreach ($CIL as $pobj) 
        		{
        			$NmChamp = $pobj->NmChamp;
                                $CIL[$NmChamp]->ValChp=$data[$NmChamp];
                                $CIL[$NmChamp]->TypEdit = 'C';
                                
                                // seulement les champs auto
                                if ( $CIL[$NmChamp]->TypeAff == "AUT" ) 
                                {
                                        // traitement champs sécurisé
                                        if( $NmChamp == 'ENT_PRIVATECOMMENT' ) 
                                        {
                                                if( $access == true) {
                                                        $tmp = str_replace('"', '\"', stripslashes($CIL[$NmChamp]->ValChp) );
                                                        echo ',"'.$tmp.'"';
                                                } else {
                                                        echo ',""';
                                                }   
                                        }
                                        else
                                        {
                                                $tmp = str_replace('"', '\"', stripslashes($CIL[$NmChamp]->ValChp) );
                                                if( $j == 0 ) {
                                                        echo '"'.$tmp.'"';
                                                } else {
                                                        echo ',"'.$tmp.'"';                                               
                                                }
                                        } 
                                        $j++;
                                } // fin si champs auto

                        } // fin du foreach
                        echo "\r\n";
                } // fin du while
        } // fin du mysql_num_rows()
}
# PARTIE 3 !!!!!!!!!!!!!!!!!!!!!
elseif( $_GET['type'] == 'personnes' )
{

	/*******************************************************
	*
	* EXTRACTIONS DES PERSONNES D'UNE SEULE ENTITE !
	*
	********************************************************/

	if($_GET['ent_id']) 
	{
		// on genere le nom
		$ent_id = (int)$_GET['ent_id'];
		$db->query('SELECT `ENT_NOMINATION`, `ENT_RAISONSOCIAL` FROM `ENTITES` WHERE `ENT_ID`="'.$ent_id.'" LIMIT 1');
		$row = $db->fetch_array();
	
		$filename = strtolower( $row['ENT_RAISONSOCIAL'].'_'.$row['ENT_NOMINATION'] );
		$filename = str_replace(' ', '_', $filename);
	
		header("Content-disposition: attachment; filename=\"extraction-personnes-entite-$filename.csv\"");
		header("Content-Type: application/force-download");
		header("Content-Transfer-Encoding: binary");
		// fin de la generation du nom
	
	

	/******************************************************************
	*
	* EXTRACTIONS DES PERSONNES DE TOUTES LES ENTITES DUNE CATEGORIE
	*
	*******************************************************************/
	} else {

		// on genere le nom
		$cat_id = (int)$_GET['cat_id'];
		$db->query('SELECT `CAT_NOM` FROM `CATEGORIES` WHERE `CAT_ID`="'.$cat_id.'" LIMIT 1');
		$row = $db->fetch_array();
	
		$filename = strtolower( $row['CAT_NOM'] );
		$filename = str_replace(' ', '_', $filename);
	
		#header("Content-disposition: attachment; filename=\"extraction-personnes-categories-$filename.csv\"");
		#header("Content-Type: application/force-download");
		#header("Content-Transfer-Encoding: binary");
		// fin de la generation du nom


		$where = get_ent_from_cat($cat_id);
		$db->query



	}
	
}


// ###############################################:#######################

include('FOOTER.php');
?>
