<?PHP

$popup = true;
include('HEADER.php');

// ##################################################################


// on redimentionne la fenetre
?>
<html>
  <head>
    <SCRIPT LANGUAGE="JavaScript">parent.window.resizeTo('700','600');</SCRIPT>
    <link rel="stylesheet" type="text/css" href="templates/style.css">
  </head>
  <body>
<?php


// Ajout de l'entitée
// -----------------------------

if($_POST) 
{
	echo '<script language="javascript">
		window.opener.location.reload();
		window.close();
      </script>';
}

// ---------------------

$db->query('INSERT INTO `ENTITEES` (CATEGORIES_CAT_ID,ENT_DTCREA,ENT_COOPE) VALUES ("'.(int)$_GET['cat_parentid'].'"i,"CURDATE()","'.$_SESSION['auth_id'].'");');
$id  = mysql_insert_id();

$sql='SELECT * FROM `ENTITEES` WHERE ENT_ID="'.$id.'"';
$CIL=InitPOReq($sql,'annuaire_externe');
$rep=$db->query($sql);
$data=$db->fetch_array();
echo '<table width="100%">';
foreach ($CIL as $pobj) {
	$CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
	EchoLig($pobj->NmChamp);
}
echo "</table>";
// fonction qui affiche une ligne de tableau
// AFfiche le champ toujours en édition, et en consult uniquement si valeur non vide
// FTE=Force Type Edit (ne tiens pas compte de ce qu'il y a ds l'objet)
function EchoLig($NmChamp,$FTE=""){
	global $CIL,$pobj;
	// FTE= Force Type Edit
	if ($FTE!="") $CIL[$NmChamp]->TypEdit=$FTE;
	if ($CIL[$NmChamp]->TypEdit!="C" || $CIL[$NmChamp]->ValChp!="") { 
	  	echo "<tr><td>".$CIL[$NmChamp]->Libelle;
		if ($CIL[$NmChamp]->TypEdit!="C" && $CIL[$NmChamp]->Comment!="") {
			echspan("legendes9px","<BR>".$CIL[$NmChamp]->Comment);
			} 
		echo "</td>\n";
		echo "<td>";
	  	// traitement valeurs avant MAJ
  	  	$CIL[$NmChamp]->InitAvMaj($_SESSION['auth_id']);
		$CIL[$NmChamp]->EchoEditAll(); // pas de champs hidden
		echo "</td></tr>\n";
	}
}

echo '</body></html>';
// ###############################################:#######################

include('FOOTER.php');
?>
