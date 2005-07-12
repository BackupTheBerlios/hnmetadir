<?PHP

$popup = true;
include('HEADER.php');

// ##################################################################


?>
<html>
  <head>
    <SCRIPT LANGUAGE="JavaScript">parent.window.resizeTo('720','600');</SCRIPT>
    <link rel="stylesheet" type="text/css" href="templates/style.css">
  </head>
  <body>

<?php



function EchoLig($NmChamp,$FTE=""){
	global $CIL,$pobj;
	// FTE= Force Type Edit
	if ($FTE!="") $CIL[$NmChamp]->TypEdit=$FTE;
	if( $CIL[$NmChamp]->Typaff_l!='' &&  ($CIL[$NmChamp]->TypEdit!="C" || $CIL[$NmChamp]->ValChp!="") ) 
	{
		// ne pas afficher les libelle des champs cachés
		if($CIL[$NmChamp]->TypeAff!="HID") {
		  	echo "<tr><td><b>".$CIL[$NmChamp]->Libelle;
			if ($CIL[$NmChamp]->TypEdit!="C" && $CIL[$NmChamp]->Comment!="") {
				echspan("legendes9px","<BR>".$CIL[$NmChamp]->Comment);
			}
		}

		echo "</b></td>\n";
		echo "<td>: ";
	  	// traitement valeurs avant MAJ
	  	$CIL[$NmChamp]->InitAvMaj($_SESSION['auth_id']);
		$CIL[$NmChamp]->EchoEditAll(); // pas de champs hidden
		echo "</td></tr>\n";
	}
}

// DEBUT -------------------------------------------------------------------------------------

if( $_POST ) // GESTION DE L'AJOUT ---------------------------------------
{

	// début traitement fichier
	// composition du nom
	// ---------------------------------------
        if( !$_SESSION['per_id'] )
        {

		$db->query("SELECT `PER_ID` from `PERSONNES` order by `PER_ID` DESC LIMIT 1");
                $rp2 = $db->fetch_array($rp1);
                $keycopy = $rp2[0]+1;
                $keycopy = $keycopy."_";

                // fin traitement fichier
                // -----------------------
	
                $sql = $db->query("SELECT `NM_CHAMP` from `DESC_TABLES` WHERE NM_TABLE='PERSONNES' AND NM_CHAMP!='TABLE0COMM' ORDER BY ORDAFF, LIBELLE");
                $PYAoMAJ = new PYAobj();
                $PYAoMAJ->NmBase=$DBName;
                $PYAoMAJ->NmTable='PERSONNES';
                $PYAoMAJ->TypEdit='';
		
                while ($data = $db->fetch_array())
                {
                        $NOMC=$data['NM_CHAMP']; // nom variable=nom du champ
                        $PYAoMAJ->NmChamp=$NOMC;
                        $PYAoMAJ->InitPO();
                        $PYAoMAJ->ValChp=$_POST[$NOMC]; // issu du formulaire

                        if ($PYAoMAJ->TypeAff=="FICFOT") 
                        {
                                if ($_FILES[$NOMC][name]!="" && $_FILES[$NOMC][error]!="0") die ("error: impossible de joindre le fichier ".$_FILES[$NOMC][name]."; sa taille est peut-etre trop importante");
                                $VarFok="Fok".$NOMC;
                                $PYAoMAJ->ValChp=($_FILES[$NOMC][tmp_name]!="" ? $_FILES[$NOMC][tmp_name] : $PYAoMAJ->ValChp);
                                $PYAoMAJ->Fok=$$VarFok;
                                $VarFname=$NOMC."_name"; // ancienne méthode
                                $PYAoMAJ->Fname=($$VarFname !="" ? $$VarFname : $_FILES[$NOMC][name]);
                                $VarFsize=$NOMC."_size";// ancienne méthode
                                $PYAoMAJ->Fsize=($$VarFsize!="" ? $$VarFsize : $_FILES[$NOMC][size]);
                                $VarOldFName="Old".$NOMC;
                                $PYAoMAJ->OFN=$$VarOldFName;

                                if ($modif==-1) 
                                { // suppression de l'enregistrement
                                        $rqncs=msq("select ".$PYAoMAJ->NmChamp." from ".$PYAoMAJ->NmTable." where $key ");
                                        $rwncs=db_fetch_row($rqncs);
                                        $PYAoMAJ->Fname=$rwncs[0];
                                }
                        }
                        $tbset=array_merge($tbset,$PYAoMAJ->RetSet($keycopy,true));

                } // fin boucle sur les champs

                $db->query('INSERT INTO `PERSONNES` SET '.tbset2set($tbset));	
                $_SESSION['per_id'] = mysql_insert_id();			
	}

        // traitement des champs spécifique
        // ----------------------------------------
        $aep_fonction = addslashes($_POST['AEP_FONCTION']);
        $aep_tel = addslashes($_POST['AEP_TEL']);
        $aep_fax = addslashes($_POST['AEP_FAX']);
        $aep_mobile = addslashes($_POST['AEP_MOBILE']);
        $aep_abrege = addslashes($_POST['AEP_ABREGE']);
        $aep_email = addslashes($_POST['AEP_EMAIL']);
        $aep_privatecomment = addslashes($_POST['AEP_PRIVATECOMMENT']);

        $set = '`AEP_FONCTION`="'.$aep_fonction.'",`AEP_TEL`="'.$aep_tel.'",`AEP_FAX`="'.$aep_fax.'",`AEP_MOBILE`="'.$aep_mobile.'",`AEP_ABREGE`="'.$aep_abrege.'",`AEP_EMAIL`="'.$aep_email.'",`AEP_PRIVATECOMMENT`="'.$aep_privatecomment.'" , `PERSONNES_PER_ID`="'.$_SESSION['per_id'].'", `ENTITEES_ENT_ID`="'.$_SESSION['ent_id'].'", `AEP_DTCREA`="CURDATE()", `AEP_COOPE`="'.$_SESSION['auth_id'].'" ';
	$db->query('INSERT INTO `AFFECTE_ENTITEES_PERSONNES` SET '.$set);

        $_SESSION['per_id'] = '';
        $_SESSION['ent_id'] = '';
	
	// ferme la fenetre & rafraichie la fenetre parent
	echo '<script language="javascript">window.opener.location.reload();window.close();</script>';

}
else // AFFICHAGE -------------------------
{
        echo '<h2>Informations communes<h2>'."\n";
        echo '<form action="popup_add_pers_2.php?action=ajout" method="post" name="theform" ENCTYPE="multipart/form-data">'."\n";

        #-- En premiere les champs commun
        // on crée un nouveau ou on part sur un existant ?
        if( !$_GET['per_id'] ) 
        {
                $sql = 'SELECT * FROM `DESC_TABLES` WHERE `NM_TABLE`="PERSONNES" AND `NM_CHAMP`!="TABLE0COMM" ORDER BY `ORDAFF`';
                $rep=$db->query($sql);        
        	while($data=$db->fetch_array())
                {
        	    	$NM_CHAMP=$data['NM_CHAMP'];
                        $CIL[$NM_CHAMP] = new PYAobj();
                	$CIL[$NM_CHAMP]->NmBase=$DBName;
                        $CIL[$NM_CHAMP]->NmTable='PERSONNES';
                	$CIL[$NM_CHAMP]->NmChamp=$NM_CHAMP;
                        $CIL[$NM_CHAMP]->TypEdit='';
                        $CIL[$NM_CHAMP]->InitPO();
                }
        } 
        else 
        {
                $_SESSION['per_id'] = (int)$_GET['per_id'];

                $sql='SELECT * FROM `PERSONNES` WHERE `PER_ID`="'.$_GET['per_id'].'"';
                $CIL=InitPOReq($sql,$DBName);
                $rep=$db->query($sql);
                $data=$db->fetch_array();
        }

    	echo '<table>';
        foreach ($CIL as $pobj) {
                if( $_GET['per_id'] ) {
                        $CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
                        $CIL[$pobj->NmChamp]->TypEdit='C';
                }
        	EchoLig($pobj->NmChamp);
        }
        echo '</table>';

        echo '<h2>Informations spécifiques à cette entitée<h2>';
        unset($CIL,$NM_CHAMP);

        // Ensuite les champs spécifiques
        $sql = 'SELECT * FROM `DESC_TABLES` WHERE `NM_TABLE`="AFFECTE_ENTITEES_PERSONNES" AND `NM_CHAMP`!="TABLE0COMM" AND (`NM_CHAMP`="AEP_FONCTION" OR `NM_CHAMP`="AEP_TEL" OR `NM_CHAMP`="AEP_FAX" OR `NM_CHAMP`="AEP_MOBILE" OR `NM_CHAMP`="AEP_ABREGE" OR `NM_CHAMP`="AEP_EMAIL" OR `NM_CHAMP`="AEP_PRIVATECOMMENT") ORDER BY `ORDAFF`';
        $db->query($sql);
        
        while( $data = $db->fetch_array() )
        {
                $NM_CHAMP=$data['NM_CHAMP'];
                $CIL[$NM_CHAMP] = new PYAobj();
                $CIL[$NM_CHAMP]->NmBase=$DBName;
                $CIL[$NM_CHAMP]->NmTable='AFFECTE_ENTITEES_PERSONNES';
                $CIL[$NM_CHAMP]->NmChamp=$NM_CHAMP;
                $CIL[$NM_CHAMP]->TypEdit='';
                $CIL[$NM_CHAMP]->InitPO();
        }
        
    	echo '<table width="100%">';
	foreach ($CIL as $pobj) 
        {
		EchoLig($pobj->NmChamp);
	}
       	echo '</table>';

	echo '<center><input type="image" src="templates/images/valide.gif"> <a href="#" onclick="window.close();"><img src="templates/images/del.gif" border="0"></center></center>'."\n";
	echo '</form>';
}

// ######################################################################

include('FOOTER.php');
?>
