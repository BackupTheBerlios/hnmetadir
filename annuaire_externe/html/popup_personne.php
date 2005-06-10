<?PHP

$popup = true;
include('HEADER.php');

// ##################################################################



// - Traitement si formulaire posté
// ------------------------------------------------------








// - Génération du formulaire
// ------------------------------------------------------

$sql='SELECT * FROM `PERSONNES` WHERE PER_ID="'.$_GET['id'].'"';
$CIL=InitPOReq($sql,'annuaire_externe');
$rep=$db->query($sql);
$data=$db->fetch_array();

echo '<form action="popup_personne.php" method="post" name="theform" ENCTYPE="multipart/form-data">';
echo "<table>";
foreach ($CIL as $pobj) {
	$CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
	EchoLig($pobj->NmChamp, '');
}
echo "</table>";

// fonction qui affiche une ligne de tableau
// AFfiche le champ toujours en Ã©dition, et en consult uniquement si valeur non vide
// FTE=Force Type Edit (ne tiens pas compte de ce qu'il y a ds l'objet)
function EchoLig($NmChamp,$FTE=""){
	global $CIL,$pobj;
	// FTE= Force Type Edit
	if ($FTE!="") $CIL[$NmChamp]->TypEdit=$FTE;
	if ($CIL[$NmChamp]->TypeAff!="HID" && ($CIL[$NmChamp]->TypEdit!="C" || $CIL[$NmChamp]->ValChp!="") ) 
	{ 
		
	  	echo "<tr><td><b>".$CIL[$NmChamp]->Libelle.'</b>';
		if ($CIL[$NmChamp]->TypEdit!="C" && $CIL[$NmChamp]->Comment!="") {
			echspan("legendes9px","<BR>".$CIL[$NmChamp]->Comment);
			} 
		echo "</td>\n";
		echo "<td><b>:</b> ";
	  	// traitement valeurs avant MAJ
  	  	$CIL[$NmChamp]->InitAvMaj($_SESSION['auth_id']);
		$CIL[$NmChamp]->EchoEditAll(); // pas de champs hidden
		echo "</td></tr>\n";
	}
}

echo '</table>';
echo '<center><br><hr width="150"><br>'."\n";
echo '<input type="image" src="templates/images/valide.gif"> <a href="#" onclick="window.close();"><img src="templates/images/del.gif" border="0"></center>'."\n";
echo '</form>';

// ######################################################################

?>
