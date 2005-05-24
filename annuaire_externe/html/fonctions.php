<?php

// - Fonction permetant de récupérer/reconstituer le chemin à partir de l'id d'une catégorie ou d'une entitée
// -- Arguments :
// --- type   : 'entitee' ou 'categorie'
// --- id     : l'id de l'objet en court
// --- chemin : Ne vaut rien par défaut

function chemin($type,$id,$chemin)
{
	global $chemin;
	
	if( $type == 'entitee' ) 
	{
		$query='SELECT `ENT_ID`,`ENT_RAISONSOCIAL`,`ENT_NOMINATION`,`ENT_PARENTID`,`CATEGORIES_CAT_ID` FROM `ENTITEES` WHERE `ENT_ID`="'.$id.'"';
		$result = mysql_query($query) or die(mysql_error());
        $row = mysql_fetch_array($result);

		$nom = stripslashes( $row['ENT_NOMINATION'] );
		$parentid = $row['ENT_PARENTID'];
		$chemin = '> <img src="templates/images/entity.png" alt=""> '.$nom.' '.$chemin;
			
		if( $parentid == 0 )
		{
			chemin('categorie', $row['CATEGORIES_CAT_ID'], $chemin);
		} 
		else 
		{
			chemin('entitee',$parentid,$chemin);
		}
	}
	
	if( $type == 'categorie' )
	{
		$query='SELECT `CAT_ID`,`CAT_NOM`,`CAT_PARENTID`,`BRANCHES_BRA_ID` FROM `CATEGORIES` WHERE `CAT_ID`="'.$id.'"';
		$result = mysql_query($query) or die(mysql_error());
        $row = mysql_fetch_array($result);

		$id = $row['CAT_ID'];
		$nom = stripslashes( $row['CAT_NOM'] );
		$parentid = $row['CAT_PARENTID'];
		$chemin = '> <img src="templates/images/folder.png" alt=""> '.$nom.' '.$chemin;
			
		if( $parentid == 0 )
		{
			$braid = $row['BRANCHES_BRA_ID'];				
			$req = mysql_query('SELECT `BRA_ID`,`BRA_NOM` FROM `BRANCHES` WHERE `BRA_ID`="'.$braid.'"');
			$nom = mysql_result($req,0,'BRA_NOM');
			$chemin = '<b>'.$nom.'</b> '.$chemin;
		} 
		else 
		{
			chemin('categorie', $parentid,$chemin);
		}
	}
	return $chemin;
}
