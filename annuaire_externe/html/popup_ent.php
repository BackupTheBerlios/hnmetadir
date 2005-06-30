<?PHP

$popup = true;
include('HEADER.php');

// ##################################################################

?>
<html>
  <head>
    <SCRIPT LANGUAGE="JavaScript">parent.window.resizeTo('700','600');</SCRIPT>
    <link rel="stylesheet" type="text/css" href="templates/style.css">
  </head>
  <body>
<?php

function EchoLig($NmChamp,$FTE="")
{
	global $CIL,$pobj, $access;
	// FTE= Force Type Edit
	if ($FTE!="") $CIL[$NmChamp]->TypEdit=$FTE;
	
	if( $CIL[$NmChamp]->Typaff_l!='' && ($CIL[$NmChamp]->TypEdit!="C" || $CIL[$NmChamp]->ValChp!="") ) 
	{
			// on vire la ligne categorie parent et entitee parent
                        $display = true;
			if($NmChamp == 'CATEGORIES_CAT_ID' || $NmChamp == 'ENT_PARENTID') $display = false;
                        if($FTE == 'C' && ereg('PROPRIETE', $NmChamp) && !$access) $display = false;
			
                        if($display == true) 
                        {
				// ne pas afficher les libelle des champs cachés
				if($CIL[$NmChamp]->TypeAff!="HID") 
				{
					echo '<tr><td><b>'.$CIL[$NmChamp]->Libelle.'</b>';
					if ($CIL[$NmChamp]->TypEdit!="C" && $CIL[$NmChamp]->Comment!="") {
						echspan("legendes9px","<BR>".$CIL[$NmChamp]->Comment);
					}
				}
				echo "</td>\n";
				echo "<td>";
				// traitement valeurs avant MAJ
				$CIL[$NmChamp]->InitAvMaj($_SESSION['auth_id']);
				$CIL[$NmChamp]->EchoEditAll(); // pas de champs hidden
				echo "</td></tr>\n";
			}
	}
}

// TRAITEMENT DU FORMULAIRE APRES POSTAGE

if($_POST) 
{

	// début traitement fichier
	// composition du nom
	// ---------------------------------------
	
	// on recupere les noms des 2 1er champs (idem aux variables)
	$rqkc  = $db->query("SELECT `NM_CHAMP` FROM `DESC_TABLES` WHERE NM_TABLE='ENTITEES' AND NM_CHAMP!='TABLE0COMM' ORDER BY ORDAFF, LIBELLE LIMIT 2");
	$nmchp = $db->fetch_array($rqkc);
	$chp   = $nmchp[0];
	$mff   = mysqff ($chp, 'ENTITEES');
	// dans mff on a les caract. de cle primaire, auto_increment, etc ... du 1er champ
	if (stristr($mff,"primary_key")) { // si 1er champ est une clé primaire
		// on regarde si c'est un auto incrément
		if (stristr($mff,"auto_increment") && $_GET['action'] == 'ajout')
		{ // si auto increment et nouvel enregistrement ou copie
			$rp1 = $db->query("SELECT $chp from `ENTITEES` order by $chp DESC LIMIT 1");
			$rp2 = $db->fetch_array($rp1);
			$keycopy = $rp2[0]+1;
			$keycopy = $keycopy."_";
		}
		else
		{ 
			// si pas auto increment ou modif, on recup la valeur
			$keycopy=$_POST[$nmchp[0]].'_'; // VALEUR du premier champ  
		}

	}
	else // si 1er champ pas cle primaire, elle est forcement constituee des 2 autres
	{
		echo '!';
		$keycopy = $$nmchp[0]; // VALEUR du premier champ
		$nmchp   = $db->fetch_array($rqkc);
		$keycopy = $keycopy."_".$$nmchp[0]."_";// VALEUR du deuxieme champ
	}


	// fin traitement fichier
	// -----------------------
	
	$sql=$db->query("SELECT `NM_CHAMP` from `DESC_TABLES` WHERE NM_TABLE='ENTITEES' AND NM_CHAMP!='TABLE0COMM' ORDER BY ORDAFF, LIBELLE");
	$PYAoMAJ=new PYAobj();
	$PYAoMAJ->NmBase='annuaire_externe';
	$PYAoMAJ->NmTable='ENTITEES';
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
	
	
	if($_GET['action'] == 'ajout') {
		$db->query("INSERT INTO `ENTITEES` SET ".tbset2set($tbset));
	} elseif($_GET['action'] == 'edition') {
		$db->query("UPDATE `ENTITEES` SET ".tbset2set($tbset)." WHERE `ENT_ID`='".(int)$_GET['id']."'");		
	}

	// ferme la fenetre & rafraichie la fenetre parent
	echo '<script language="javascript">window.opener.location.reload();window.close();</script>';

}


// Ajout de l'entitée
// -----------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------
if($_GET['action'] == 'ajout') 
{

	$sql = 'SELECT * FROM `DESC_TABLES` WHERE `NM_TABLE`="ENTITEES" AND `NM_CHAMP`!="TABLE0COMM" ORDER BY `ORDAFF`';
    	$rep=$db->query($sql);
	        
	while($data=$db->fetch_array())
	{
	    	$NM_CHAMP=$data['NM_CHAMP'];
	    	$CIL[$NM_CHAMP] = new PYAobj();
      		$CIL[$NM_CHAMP]->NmBase='annuaire_externe';
        	$CIL[$NM_CHAMP]->NmTable='ENTITEES';
		$CIL[$NM_CHAMP]->NmChamp=$NM_CHAMP;
		$CIL[$NM_CHAMP]->TypEdit='';
		$CIL[$NM_CHAMP]->InitPO();
	}

	$CIL['CATEGORIES_CAT_ID']->ValChp=$_GET['cat_parent'];

	echo '<form action="popup_ent.php?action=ajout" method="post" name="theform" ENCTYPE="multipart/form-data">';
    	echo '<table width="100%">';
    	foreach ($CIL as $pobj) {
        	EchoLig($pobj->NmChamp);
    	}

	echo '</table>';

	// on ajoute en hidden la categorie parent et l'entitée parent
	echo '<input type="hidden" name="CATEGORIES_CAT_ID" value="'.$_GET['cat_parentid'].'">';
	echo '<input type="hidden" name="ENT_PARENTID" value="'.$_GET['ent_parentid'].'">';
	
	echo '<center><input type="image" src="templates/images/valide.gif"> <a href="#" onclick="window.close();"><img src="templates/images/del.gif" border="0"></center></center>'."\n";
	echo '</form>';

} 
// Edition ou Consultation d'une entité
// -------------------------------------------------------------------------------------------
// -------------------------------------------------------------------------------------------
elseif($_GET['action'] == 'edition' || $_GET['action'] == 'consultation')
{

	$sql='SELECT * FROM `ENTITEES` WHERE ENT_ID="'.$_GET['id'].'"';
	$CIL=InitPOReq($sql,'annuaire_externe');
	$rep=$db->query($sql);
	$data=$db->fetch_array();
	
	echo '<form action="popup_ent.php?action=edition&id='.(int)$_GET['id'].'" method="post" name="theform" ENCTYPE="multipart/form-data">';
	echo '<table width="100%">';

        $access = $user->HaveAccess($data['CATEGORIES_CAT_ID'], 'R');
        
	foreach ($CIL as $pobj) 
        {
		$CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
                
                if( $_GET['action'] == 'consultation' ) {
		      EchoLig($pobj->NmChamp, 'C');
                } else {
		      EchoLig($pobj->NmChamp);                        
                }
	}
	echo "</table>";

        // on ajoute en hidden la categorie parent et l'entitée parent
        echo '<input type="hidden" name="CATEGORIES_CAT_ID" value="'.$data['CATEGORIES_CAT_ID'].'">';
        echo '<input type="hidden" name="ENT_PARENTID" value="'.$data['ENT_PARENTID'].'">';

        if( $_GET['action'] == 'consultation' ) {
                echo '<center><br><hr width="400"><br><br><a href="#" onclick="window.print();"><img src="templates/images/imprimante.gif" border="0"></a></center>'."\n";
        } else {
                echo '<center><input type="image" src="templates/images/valide.gif"> <a href="#" onclick="window.close();"><img src="templates/images/del.gif" border="0"></a></center>'."\n";                        
        }        
	echo '</form>';
}
else
{
	// pas d'action
	echo '<script language="javascript">window.close();</script>';
}


echo '</body></html>';

// ###############################################:#######################

include('FOOTER.php');
?>
