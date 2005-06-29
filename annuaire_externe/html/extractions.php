<?PHP

$popup = true;
include('HEADER.php');

// ##################################################################

# DIVIS� EN 3 SOUS PARTIES !
# 1) Extration des cat�gories seulement
# 2) Extrations des entit�es 
# 3) Extrations des personnes d'une entit�e


# PARTIE 1 !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
# !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

if( $_GET['type'] == 'categories' )
{
	$debut = (int)$_GET['debut'];
	$db->query('SELECT `CAT_NOM` FROM `CATEGORIES` WHERE `CAT_ID`="'.$debut.'" LIMIT 1');
	$row = $db->fetch_array();

	$filename = strtolower( $row['CAT_NOM'] );
	$filename = str_replace(' ', '_', $filename);

	header("Content-disposition: attachment; filename=\"extraction-categories-$filename.csv\"");
	header("Content-Type: application/force-download");
	header("Content-Transfer-Encoding: binary");

	
	// on r�cupere les ids des sous cat en dessous
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
			echo $position.$nom."\n";
                        $id  = mysql_result($result,$i,"CAT_ID");
                        GetCatsForCSV($id, $position);

                }
        }

        GetCatsForCSV($debut, $position);

}
# PARTIE 2 !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
# !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
elseif( $_GET['type'] == 'entitees' )
{

        // on genere le nom
        $debut = (int)$_GET['debut'];
        $db->query('SELECT `CAT_NOM` FROM `CATEGORIES` WHERE `CAT_ID`="'.$debut.'" LIMIT 1');
        $row = $db->fetch_array();

        $filename = strtolower( $row['CAT_NOM'] );
        $filename = str_replace(' ', '_', $filename);

#        header("Content-disposition: attachment; filename=\"extraction-entitees-$filename.csv\"");
#        header("Content-Type: application/force-download");
#        header("Content-Transfer-Encoding: binary");
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

        // comme on affiche toute les entit�es de toutes les sous cat�gories qui se trouve en dessous de l'id
        // les libell�s des champs sp�ciaux ne seront pas pareille car il risque d'y avoir des sous cat�gories debutant
        // sur des champs sp�ciaux differents
        echo ',"Champs Sp�cial 1","Champs Sp�cial 2","Champs Sp�cial 3","Champs Sp�cial 4","Champs Sp�cial 5";'."\n";
        // fin de l'affichage de la premiere ligne

        // on rempli le tableau
        $tabcat = array();
	$where = get_subcats($debut);

        unset($ECT, $NM_CHAMP);
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
                                        // traitement champs s�curis�
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
                                                $j++;
                                        } 
                                } // fin si champs auto

                                // si on est dans les champs sp�ciaux et que je suis admin ou que j'ai le droit
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
                                }// fin du if pour les champs s�curis�s

                        } // fin du foreach
                        echo ";\n";
                } // fin du while
        } // fin du mysql_num_rows()
}
# PARTIE 3 !!!!!!!!!!!!!!!!!!!!!
elseif( $_GET['type'] == 'personnes' )
{

}


// ###############################################:#######################

include('FOOTER.php');
?>
