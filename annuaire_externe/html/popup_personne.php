<?PHP

include('HEADER.php');
$popup = true;

// ##################################################################

$tpl->set_file('FileRef','consulter.html');


$sql='SELECT * FROM `PERSONNES` WHERE PER_ID="'.$_GET['id'].'"';
$CIL=InitPOReq($sql,'annuaire_externe');
$rep=$db->query($sql);
$data=$db->fetch_array();

echo "<table>";
foreach ($CIL as $pobj) {
	$CIL[$pobj->NmChamp]->ValChp=$data[$pobj->NmChamp];
	EchoLig($pobj->NmChamp, 'C');
}
echo "</table>";

// fonction qui affiche une ligne de tableau
// AFfiche le champ toujours en �dition, et en consult uniquement si valeur non vide
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

$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
