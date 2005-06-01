<?php

// - Fonction permetant de récupérer/reconstituer le chemin à partir de l'id d'une catégorie ou d'une entitée
// -- Arguments :
// --- id     : l'id de l'objet en court

function chemin_entitee($id)
{
	global $tabent;

	array_push($tabent, $id);

	$query='SELECT `ENT_ID`,`ENT_RAISONSOCIAL`,`ENT_NOMINATION`,`ENT_PARENTID`,`CATEGORIES_CAT_ID` FROM `ENTITEES` WHERE `ENT_ID`="'.$id.'"';
	$result = mysql_query($query) or die(mysql_error());
       	$row = mysql_fetch_array($result);

	$nom = stripslashes( $row['ENT_NOMINATION'] );
	$parentid = $row['ENT_PARENTID'];
		
	if( $parentid == 0 )
	{
		chemin_categorie($row['CATEGORIES_CAT_ID']);
	} 
	else 
	{
		chemin_entitee($parentid);
	}
	return $tabent;
}

function chemin_categorie($id)
{
	global $tabcat;

        // ajout dans ce tableau temporaire
        array_push($tabcat, $id);

	$query='SELECT `CAT_ID`,`CAT_PARENTID` FROM `CATEGORIES` WHERE `CAT_ID`="'.$id.'"';
	$result = mysql_query($query) or die(mysql_error());
        $row = mysql_fetch_array($result);
	
	$id = $row['CAT_ID'];
	$parentid = $row['CAT_PARENTID'];
	
			
	if( $parentid != 0 )
	{
		chemin_categorie($parentid);
	}

	return $tabcat;
}
