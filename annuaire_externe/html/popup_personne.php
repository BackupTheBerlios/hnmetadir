<?PHP

include('HEADER.php');
$popup = true;

// ##################################################################



// - Traitement si formulaire posté
// ------------------------------------------------------





// - Génération du formulaire
// ------------------------------------------------------

$NM_TABLE="ENTITEES";  
// construction du set
$sql= $db->query("SELECT NM_CHAMP from `DESC_TABLES` where NM_TABLE='$NM_TABLE' ORDER BY ORDAFF, LIBELLE");

$CIL=InitPOReq($sql,'annuaire_externe');
$rep=$db->query($sql);
$data=$db->fetch_array();

echo '<form action="popup_personne.php" method="post" name="theform" ENCTYPE="multipart/form-data">';
echo "<table>";
foreach ($CIL as $pobj) {
	$CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
	EchoLig($pobj->NmChamp, 'C');
}
echo "</table>";

// fonction qui affiche une ligne de tableau
// AFfiche le champ toujours en Ã©dition, et en consult uniquement si valeur non vide
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

echo '</form>';

// ######################################################################

?>
