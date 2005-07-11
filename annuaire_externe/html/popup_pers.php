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


if( $_GET['action'] == 'supprimer' ) 
{
	$db->query('DELETE FROM `AFFECTE_ENTITEES_PERSONNES` WHERE `ENTITEES_ENT_ID`="'.$_GET['ent_id'].'" AND `PERSONNES_PER_ID`="'.$_GET['per_id'].'" '); 
	// ferme la fenetre & rafraichie la fenetre parent
	echo '<script language="javascript">window.opener.location.reload();window.close();</script>';



}
elseif( $_POST ) // GESTION DE L'AJOUT ---------------------------------------
{

	// début traitement fichier
	// composition du nom
	// ---------------------------------------

	// on recupere les noms des 2 1er champs (idem aux variables)
	$rqkc  = $db->query("SELECT `NM_CHAMP` FROM `DESC_TABLES` WHERE NM_TABLE='PERSONNES' AND NM_CHAMP!='TABLE0COMM' ORDER BY ORDAFF, LIBELLE LIMIT 2");
	$nmchp = $db->fetch_array($rqkc);
	$chp   = $nmchp[0];
	$mff   = mysqff ($chp, 'PERSONNES');
	// dans mff on a les caract. de cle primaire, auto_increment, etc ... du 1er champ
	if (stristr($mff,"primary_key")) { // si 1er champ est une clé primaire
		// on regarde si c'est un auto incrément
		if (stristr($mff,"auto_increment") && $_GET['action'] == 'ajout')
		{ // si auto increment et nouvel enregistrement ou copie
			$rp1 = $db->query("SELECT $chp from `PERSONNES` order by $chp DESC LIMIT 1");
			$rp2 = $db->fetch_array($rp1);
			$keycopy = $rp2[0]+1;
			$keycopy = $keycopy."_";
		}
		else
		{ 
			// si pas auto increment ou modif, on recup la valeur
			$keycopy=$$nmchp[0]."_"; // VALEUR du premier champ  
		}

	}
	else // si 1er champ pas cle primaire, elle est forcement constituee des 2 autres
	{
		$keycopy = $$nmchp[0]; // VALEUR du premier champ
		$nmchp   = $db->fetch_array($rqkc);
		$keycopy = $keycopy."_".$$nmchp[0]."_";// VALEUR du deuxieme champ
	}


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

        $set= substr($set,0,-2); // enlève la dernière virgule et esp en trop à la fin
        $db->query("UPDATE `PERSONNES` SET ".tbset2set($tbset)." WHERE `PER_ID`='".$_GET['per_id']."'");				


        // traitement des champs spécifique
        // ----------------------------------------

        $aep_fonction = addslashes($_POST['AEP_FONCTION']);
        $aep_tel = addslashes($_POST['AEP_TEL']);
        $aep_fax = addslashes($_POST['AEP_FAX']);
        $aep_mobile = addslashes($_POST['AEP_MOBILE']);
        $aep_abrege = addslashes($_POST['AEP_ABREGE']);
        $aep_email = addslashes($_POST['AEP_EMAIL']);
        $aep_privatecomment = addslashes($_POST['AEP_PRIVATECOMMENT']);
        $set = '`AEP_FONCTION`="'.$aep_fonction.'",`AEP_TEL`="'.$aep_tel.'",`AEP_FAX`="'.$aep_fax.'",`AEP_MOBILE`="'.$aep_mobile.'",`AEP_ABREGE`="'.$aep_abrege.'",`AEP_EMAIL`="'.$aep_email.'",`AEP_PRIVATECOMMENT`="'.$aep_privatecomment.'"';


        $db->query('UPDATE `AFFECTE_ENTITEES_PERSONNES` SET '.$set.' WHERE `PERSONNES_PER_ID`="'.$_GET['per_id'].'" AND `ENTITEES_ENT_ID`="'.$_GET['ent_id'].'" ');


	// ferme la fenetre & rafraichie la fenetre parent
	echo '<script language="javascript">window.opener.location.reload();window.close();</script>';

}
else // AFFICHAGE -------------------------
{
        echo '<h2>Informations communes<h2>';
        echo '<form action="popup_pers.php?per_id='.$_GET['per_id'].'&ent_id='.$_GET['ent_id'].'" method="post" name="theform" ENCTYPE="multipart/form-data">';

        #-- En premiere les champs commun

        $sql='SELECT * FROM `PERSONNES` WHERE `PER_ID`="'.$_GET['per_id'].'"';
        $CIL=InitPOReq($sql,$DBName);
        $rep=$db->query($sql);
        $data=$db->fetch_array();

    	echo '<table>';
        foreach ($CIL as $pobj) {
                $CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
                if( $_GET['action'] == 'consultation' ) $CIL[$pobj->NmChamp]->TypEdit='C';
        	EchoLig($pobj->NmChamp);
        }
        echo '</table>';

        echo '<h2>Informations spécifiques à cette entitée<h2>';
        unset($CIL,$NM_CHAMP);

        // Ensuite les champs spécifiques
        $sql = 'SELECT `AEP_FONCTION`, `AEP_TEL`, `AEP_FAX`, `AEP_MOBILE`, `AEP_ABREGE`, `AEP_EMAIL`, `AEP_PRIVATECOMMENT` FROM `AFFECTE_ENTITEES_PERSONNES` WHERE `ENTITEES_ENT_ID`="'.$_GET['ent_id'].'" AND `PERSONNES_PER_ID`="'.$_GET['per_id'].'" ';

        $CIL=InitPOReq($sql,$DBName);
        $rep=$db->query($sql);
        $data=$db->fetch_array();

    	echo '<table>';
        foreach ($CIL as $pobj) {
                $CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
                if( $_GET['action'] == 'consultation' ) $CIL[$pobj->NmChamp]->TypEdit='C';
        	EchoLig($pobj->NmChamp);
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
