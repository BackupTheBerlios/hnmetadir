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



function EchoLig($NmChamp,$FTE="")
{
	global $CIL, $access;

	// FTE= Force Type Edit
	if ($FTE!="") $CIL[$NmChamp]->TypEdit=$FTE;
	
	if( $CIL[$NmChamp]->TypEdit!="C" || $CIL[$NmChamp]->ValChp!="" )  
	{
			// ne pas afficher les libelle des champs cachés
			if($CIL[$NmChamp]->TypeAff!="HID") 
			{
				echo '<tr><td><b>'.$CIL[$NmChamp]->Libelle.'</b>';
				if ($CIL[$NmChamp]->TypEdit!="C" && $CIL[$NmChamp]->Comment!="") {
					echspan("legendes9px","<BR>".$CIL[$NmChamp]->Comment);
				}
				echo "</td>\n";
                                echo "<td>";
                                // traitement valeurs avant MAJ
                                $CIL[$NmChamp]->InitAvMaj($_SESSION['auth_id']);
                                $CIL[$NmChamp]->EchoEditAll(); // pas de champs hidden
                                echo "</td></tr>\n";
			} // fin si chp pas cach�
			else 
                        { // champs cach�s
				$CIL[$NmChamp]->InitAvMaj($_SESSION['auth_id']);
				$CIL[$NmChamp]->EchoEditAll();
			}

	}
}

// DEBUT -------------------------------------------------------------------------------------


if( $_GET['action'] == 'supprimer' ) 
{
	$db->query('DELETE FROM `AFFECTE_ENTITES_PERSONNES` WHERE `ENTITES_ENT_ID`="'.$_GET['ent_id'].'" AND `PERSONNES_PER_ID`="'.$_GET['per_id'].'" '); 
	// ferme la fenetre & rafraichie la fenetre parent
	echo '<script language="javascript">window.opener.location.reload();window.close();</script>';



}
elseif( $_POST ) // GESTION DE L'AJOUT ---------------------------------------
{
	// d�but traitement fichier
	// composition du nom
	// ---------------------------------------
	$keycopy=$_POST['PER_ID']."_"; // VALEUR du premier champ  


	// fin traitement fichier
	// -----------------------
	
	$sql=$db->query("SELECT `NM_CHAMP` from `DESC_TABLES` WHERE NM_TABLE='PERSONNES' AND NM_CHAMP!='TABLE0COMM' ORDER BY ORDAFF, LIBELLE");
	$PYAoMAJ=new PYAobj();
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
     			$VarFname=$NOMC."_name"; // ancienne m�thode
     			$PYAoMAJ->Fname=($$VarFname !="" ? $$VarFname : $_FILES[$NOMC][name]);
     			$VarFsize=$NOMC."_size";// ancienne m�thode
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
  		$tbset = array_merge($tbset,$PYAoMAJ->RetSet($keycopy,true));
		
	} // fin boucle sur les champs
	
        $db->query("UPDATE `PERSONNES` SET ".tbset2set($tbset)." WHERE `PER_ID`='".$_GET['per_id']."'");				

        // traitement des champs sp�cifique
        // ----------------------------------------

        $aep_fonction = addslashes($_POST['AEP_FONCTION']);
        $aep_tel = addslashes($_POST['AEP_TEL']);
        $aep_fax = addslashes($_POST['AEP_FAX']);
        $aep_mobile = addslashes($_POST['AEP_MOBILE']);
        $aep_abrege = addslashes($_POST['AEP_ABREGE']);
        $aep_email = addslashes($_POST['AEP_EMAIL']);
        $aep_privatecomment = addslashes($_POST['AEP_PRIVATECOMMENT']);
        $set = '`AEP_FONCTION`="'.$aep_fonction.'",`AEP_TEL`="'.$aep_tel.'",`AEP_FAX`="'.$aep_fax.'",`AEP_MOBILE`="'.$aep_mobile.'",`AEP_ABREGE`="'.$aep_abrege.'",`AEP_EMAIL`="'.$aep_email.'",`AEP_PRIVATECOMMENT`="'.$aep_privatecomment.'", `AEP_DTCREA`=CURDATE(),`AEP_COOPE`="'.$_SESSION['auth_id'].'"';


        $db->query('UPDATE `AFFECTE_ENTITES_PERSONNES` SET '.$set.' WHERE `PERSONNES_PER_ID`="'.$_GET['per_id'].'" AND `ENTITES_ENT_ID`="'.$_GET['ent_id'].'" ');


	// ferme la fenetre & rafraichie la fenetre parent
	echo '<script language="javascript">window.opener.location.reload();window.close();</script>';

}
else // AFFICHAGE -------------------------
{
        echo '<h2>Informations communes<h2>';
        echo '<form action="popup_pers.php?per_id='.$_GET['per_id'].'&ent_id='.$_GET['ent_id'].'" method="post" name="theform" ENCTYPE="multipart/form-data">';

        #-- En premiere les champs commun

        $sql1='SELECT * FROM `PERSONNES` WHERE `PER_ID`="'.$_GET['per_id'].'"';
        $CIL=InitPOReq($sql1,$DBName);
        $rep=$db->query($sql1);
        $data=$db->fetch_array();

    	echo '<table>';
        foreach ($CIL as $pobj) {
                $CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
                if( $_GET['action'] == 'consultation' ) $CIL[$pobj->NmChamp]->TypEdit='C';
        	EchoLig($pobj->NmChamp);
        }
	echo '<input type="hidden" name="PER_ID" value="'.$_GET['per_id'].'">'."\n";
        echo '</table>'."\n";

        echo '<h2>Informations sp�cifiques � cet entit�</h2>';

        unset($CIL,$NM_CHAMP,$tmp);

        // Ensuite les champs sp�cifiques
        $sql2 = 'SELECT `AEP_FONCTION`, `AEP_TEL`, `AEP_FAX`, `AEP_MOBILE`, `AEP_ABREGE`, `AEP_EMAIL`, `AEP_DTCREA`, `AEP_DTMAJ`, `AEP_COOPE` FROM `AFFECTE_ENTITES_PERSONNES` WHERE `ENTITES_ENT_ID`="'.$_GET['ent_id'].'" AND `PERSONNES_PER_ID`="'.$_GET['per_id'].'" ';

        $CIL=InitPOReq($sql2,$DBName);
        $rep=$db->query($sql2);
        $data=$db->fetch_array();

        echo '<table>';
        foreach ($CIL as $pobj) {
                $CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
                if( $_GET['action'] == 'consultation' ) $CIL[$pobj->NmChamp]->TypEdit='C';
                EchoLig($pobj->NmChamp);
        }
	        

    	// on v�rifie que l'utilisateur est bien les droits de lecture
        $db->query('SELECT `CATEGORIES_CAT_ID` FROM `ENTITES` WHERE `ENT_ID`="'.$_GET['ent_id'].'" ');
        $tmp = $db->fetch_array();
    
        if( $user->HaveAccess($tmp[0], 'R') == true )
        {
            unset($CIL,$NM_CHAMP,$tmp);
            $sql3 = 'SELECT `AEP_PRIVATECOMMENT` FROM `AFFECTE_ENTITES_PERSONNES` WHERE `ENTITES_ENT_ID`="'.$_GET['ent_id'].'" AND `PERSONNES_PER_ID`="'.$_GET['per_id'].'" ';
	
            $CIL=InitPOReq($sql3,$DBName);
            $rep=$db->query($sql3);
            $data=$db->fetch_array();

            foreach ($CIL as $pobj) {
                $CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
                if( $_GET['action'] == 'consultation' ) $CIL[$pobj->NmChamp]->TypEdit='C';
                EchoLig($pobj->NmChamp);
            }
	}

        echo '</table>';
	
        if( $_GET['action'] != 'consultation' ) {
	       echo '<center><input type="image" src="templates/images/valide.gif"> <a href="#" onclick="window.close();"><img src="templates/images/del.gif" border="0"></center></center>'."\n";
        } else {
                echo '<center><br><hr width="400"><br><br><a href="#" onclick="window.print();"><img src="templates/images/imprimante.gif" border="0"></a></center>'."\n";
        }

	echo '</form>';
        
}

// ######################################################################

include('FOOTER.php');
?>
