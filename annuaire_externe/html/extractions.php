<?PHP
	  header('Content-disposition: filename=extractPYA.tsv');
      //header("Content-disposition: attachment; filename=extraction-personnes-$filename.csv");
	header('Content-type: application/octetstream');
	header('Content-type: application/ms-excel');
	header('Pragma: no-cache');
	header('Expires: 0');

$popup = true;
include('HEADER.php');

// ##################################################################

# DIVISé EN 3 SOUS PARTIES !
# 1) Extration des catégories seulement
# 2) Extrations des entitées 
# 3) Extrations des personnes d'une entitée


# PARTIE 1 !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
# !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

if( $_GET['type'] == 'categories' )
{
	$cat_id = (int)$_GET['cat_id'];
	$db->query('SELECT `CAT_NOM` FROM `CATEGORIES` WHERE `CAT_ID`="'.$cat_id.'" LIMIT 1');
	$row = $db->fetch_array();

	$filename = strtolower( $row['CAT_NOM'] );
	$filename = str_replace(' ', '_', $filename);

//        header("Content-Type: application/force-download");
//        header("Content-Transfer-Encoding: binary");
	
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
# PARTIE 2 !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
# !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
elseif( $_GET['type'] == 'entitees' )
{

        // on genere le nom
        $cat_id = (int)$_GET['cat_id'];
        $db->query('SELECT `CAT_NOM` FROM `CATEGORIES` WHERE `CAT_ID`="'.$cat_id.'" LIMIT 1');
        $row = $db->fetch_array();

        $filename = strtolower( $row['CAT_NOM'] );
        $filename = str_replace(' ', '_', $filename);

        header("Content-disposition: attachment; filename=\"extraction-personnes-$filename.csv\"");
        header("Content-Type: application/force-download");
        header("Content-Transfer-Encoding: binary");
        // fin de la generation du nom

        // on affiche la premiere ligne avec les noms des champs
        $NM_TABLE = 'ENTITEES';
        $db->query("SELECT `NM_CHAMP` from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' ORDER BY ORDAFF, LIBELLE");

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
                if ($PYAObj->TypeAff == "AUT" && $PYAObj->NmChamp != 'ENT_PROPRIETE1' && $PYAObj->NmChamp != 'ENT_PROPRIETE2' && $PYAObj->NmChamp != 'ENT_PROPRIETE3' && $PYAObj->NmChamp != 'ENT_PROPRIETE4' && $PYAObj->NmChamp != 'ENT_PROPRIETE5' ) {
                        if( $i == 0 ) {
                                echo '"'.$PYAObj->Libelle.'"';
                        } else {
                                echo ',"'.$PYAObj->Libelle.'"';
                        }
                        $i++;
                }
        }

        // comme on affiche toute les entitées de toutes les sous catégories qui se trouve en dessous de l'id
        // les libellés des champs spéciaux ne seront pas pareille car il risque d'y avoir des sous catégories debutant
        // sur des champs spéciaux differents
        echo ',"Champs Spécial 1","Champs Spécial 2","Champs Spécial 3","Champs Spécial 4","Champs Spécial 5";'."\r\n";
        // fin de l'affichage de la premiere ligne

        // on rempli le tableau
        $tabcat = array();
	$where = get_subcats($cat_id);

        $sql = 'SELECT * FROM `ENTITEES` WHERE `CATEGORIES_CAT_ID` IN '.$where;
	$CIL=InitPOReq($sql,$DBName);
	$rep=$db->query($sql);


        if( $db->num_rows() )
        {
                while( $data = $db->fetch_array() )
                {
                        $j=0;
                        $access = $user->HaveAccess($data['CATEGORIES_CAT_ID'], 'R');
        		foreach ($CIL as $pobj) 
        		{
        			$NmChamp = $pobj->NmChamp;
                                $CIL[$NmChamp]->ValChp=$data[$NmChamp];
                                $CIL[$NmChamp]->TypEdit = 'C';
                                
                                // seulement les champs auto
                                if ($CIL[$NmChamp]->TypeAff == "AUT" && $NmChamp != 'ENT_PROPRIETE1' && $NmChamp != 'ENT_PROPRIETE2' && $NmChamp != 'ENT_PROPRIETE3' && $NmChamp != 'ENT_PROPRIETE4' && $NmChamp != 'ENT_PROPRIETE5' ) 
                                {
                                        // traitement champs sécurisé
                                        if( $NmChamp == 'ENT_PRIVATECOMMENT' ) 
                                        {
                                                if( $_SESSION['auth_login'] == 'admin' || $CIL['ENT_PRIVATECOMMENT']->ValChp == $_SESSION['auth_id']) {
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

                                // si on est dans les champs spéciaux et que je suis admin ou que j'ai le droit
                                if( $NmChamp == 'ENT_PROPRIETE1' || $NmChamp == 'ENT_PROPRIETE2' || $NmChamp == 'ENT_PROPRIETE3' || $NmChamp == 'ENT_PROPRIETE4' || $NmChamp == 'ENT_PROPRIETE5' )
                                {
                                        if( $_SESSION['auth_login'] == 'admin' || $access == 'true' )
                                        { 
                                                 # TODO : TRAITEMENT FICHIER SPECIAUX
                                                $tmp = str_replace('"', '\"', stripslashes($CIL[$NmChamp]->ValChp) );
                                                echo ',"'.$tmp.'"';
                                        }
                                        else
                                        {
                                                echo ',""';
                                        }
                                }// fin du if pour les champs sécurisés

                        } // fin du foreach
                        echo ";\r\n";
                } // fin du while
        } // fin du mysql_num_rows()
}
# PARTIE 3 !!!!!!!!!!!!!!!!!!!!!
elseif( $_GET['type'] == 'personnes' )
{
        // on genere le nom
        $ent_id = (int)$_GET['ent_id'];
        $db->query('SELECT `ENT_NOMINATION`, `ENT_RAISONSOCIAL` FROM `ENTITEES` WHERE `ENT_ID`="'.$ent_id.'" LIMIT 1');
        $row = $db->fetch_array();

        $filename = strtolower( $row['ENT_RAISONSOCIAL'].'_'.$row['ENT_NOMINATION'] );
        $filename = str_replace(' ', '_', $filename);

#        header("Content-disposition: attachment; filename=\"extraction-personnes-$filename.csv\"");
#        header("Content-Type: application/force-download");
#        header("Content-Transfer-Encoding: binary");


        // fin de la generation du nom

        // on affiche la premiere ligne avec les noms des champs
        $NM_TABLE = 'PERSONNES';
        $db->query("SELECT `NM_CHAMP` from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' ORDER BY ORDAFF, LIBELLE");

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
                if ($PYAObj->TypeAff == "AUT") 
                {
                        if( $i == 0 ) {
                                echo '"'.$PYAObj->Libelle.'"';
                        } else {
                                echo ',"'.$PYAObj->Libelle.'"';
                        }
                        $i++;
                }
        }

        // on affiche les nom des champs spécifique
	$NM_TABLE = 'AFFECTE_ENTITEES_PERSONNES';
	$db->query('SELECT * FROM `DESC_TABLES` WHERE `NM_TABLE`="AFFECTE_ENTITEES_PERSONNES" AND `NM_CHAMP`!="TABLE0COMM" AND (`NM_CHAMP`="AEP_FONCTION" OR `NM_CHAMP`="AEP_TEL" OR `NM_CHAMP`="AEP_FAX" OR `NM_CHAMP`="AEP_MOBILE" OR `NM_CHAMP`="AEP_ABREGE" OR `NM_CHAMP`="AEP_EMAIL" OR `NM_CHAMP`="AEP_PRIVATECOMMENT") ORDER BY `ORDAFF`');
        while ( $CcChp = $db->fetch_array() )  {
                $NM_CHAMP=$CcChp[0];    
                $ECT[$NM_CHAMP]=new PYAobj();
                $ECT[$NM_CHAMP]->NmBase=$DBName; 
                $ECT[$NM_CHAMP]->NmTable=$NM_TABLE;                                                                            $ECT[$NM_CHAMP]->NmChamp=$NM_CHAMP;
                $ECT[$NM_CHAMP]->TypEdit='C';   
                $ECT[$NM_CHAMP]->InitPO();
        }

	foreach ($ECT as $PYAObj) {
		if ($PYAObj->TypeAff == "AUT") echo '"'.$PYAObj->Libelle.'"';
	}


        echo ";\r\n";
        // fin de l'affichage de la premiere ligne

        // on récupère la catégorie parent avant tout pour connaitre les droits
        $db->query('SELECT `CATEGORIES_CAT_ID` FROM `ENTITEES` WHERE `ENT_ID`="'.$ent_id.'" LIMIT 1');
        $row = $db->fetch_array();
        $access = $user->HaveAccess($row['CATEGORIES_CAT_ID'], 'L');

        $sql = 'SELECT * FROM `PERSONNES`, `AFFECTE_ENTITEES_PERSONNES` WHERE `PERSONNES_PER_ID`=`PER_ID` AND `ENTITEES_ENT_ID`="'.$ent_id.'"';
	$CIL=InitPOReq($sql,$DBName);
	$rep=$db->query($sql);


        if( $db->num_rows() )
        {
                while( $data = $db->fetch_array() )
                {
                        $j=0;
                        $listerouge = $data['PER_LISTEROUGE'];
        		foreach ($CIL as $pobj) 
        		{ 
                                $NmChamp = $pobj->NmChamp;
                                $CIL[$NmChamp]->ValChp=$data[$NmChamp];
                                $CIL[$NmChamp]->TypEdit = 'C';

                                // on ne traite que les champs de la fiche personne
                                if( substr($NmChamp, 0, 4) == 'PER_' )
                                {
                                        // seulement les champs auto
                                        if ($CIL[$NmChamp]->TypeAff == "AUT") 
                                        {
                                                // traitement champs sécurisé
                                                if( $NmChamp == 'PER_TEL' || $NmChamp == 'PER_FAX' || $NmChamp == 'PER_MOBILE' || $NmChamp == 'PER_ABREGE' ) 
                                                {
                                                        if( $listrouge == 'O' && ($_SESSION['auth_login'] == 'admin' || $access == 'true') ) {
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
                                } // fin si 'PER'
                        } // fin du foreach
                        echo ";\r\n";
                } // fin du while
        } // fin du mysql_num_rows()
}


// ###############################################:#######################

include('FOOTER.php');
?>
