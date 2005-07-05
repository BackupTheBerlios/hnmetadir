<?php

// - Fonction permetant de rÃ©cupÃ©rer/reconstituer le chemin Ã  partir de l'id d'une catÃ©gorie ou d'une entitÃ©e
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

// - Fonction permettant de récuperer toutes les sous catégorie à partir d'un point précis d'une branche
//   pour supression. Revoie un WHERe tout fait
function get_subcats($id)
{
	global $tabcat;
	array_push($tabcat, $id);

	$query='SELECT `CAT_ID`,`CAT_PARENTID` FROM `CATEGORIES` WHERE `CAT_PARENTID`="'.$id.'"';
	$result = mysql_query($query) or die(mysql_error());
	$n = mysql_num_rows($result);

	for ($i=0; $i<$n; $i++)
	{
		$id  = mysql_result($result,$i,"CAT_ID"); 
		get_subcats($id);
	}

	for($i=0;$i<count($tabcat);$i++)
	{
		if($i == 0 ) {
			$tmp .= '('.$tabcat[$i];
		} else {
			$tmp .= ','.$tabcat[$i];
		}
	}
	return $tmp.')';
}

function get_subents($id)
{
	global $tabent;
	array_push($tabent, $id);

	$query='SELECT `ENT_ID`,`ENT_PARENTID` FROM `ENTITEES` WHERE `ENT_PARENTID`="'.$id.'"';
	$result = mysql_query($query) or die(mysql_error());
	$n = mysql_num_rows($result);

	for ($i=0; $i<$n; $i++)
	{
		$id  = mysql_result($result,$i,"ENT_ID"); 
		get_subents($id);
	}

	for($i=0;$i<count($tabent);$i++)
	{
		if($i == 0 ) {
			$tmp .= '('.$tabent[$i];
		} else {
			$tmp .= ','.$tabent[$i];
		}
	}
	return $tmp.')';
}

// Fonctionpermettant de récuperer juste les ids des sous catégories
// --------------------------------------------------------------------------------------------

function GetSubCats($id, $perm)
{
	global $tabcat;
	array_push($tabcat, array('id'=>$id, 'perm'=>$perm) );

	$query='SELECT `CAT_ID`,`CAT_PARENTID`,`CAT_ADMIN` FROM `CATEGORIES` WHERE `CAT_PARENTID`="'.$id.'"';
	$result = mysql_query($query) or die(mysql_error());
	$n = mysql_num_rows($result);

	for ($i=0; $i<$n; $i++)
	{
		$id  = mysql_result($result,$i,"CAT_ID");
                $admin  = mysql_result($result,$i,"CAT_ADMIN");
		GetSubCats($id, $perm);
	}
	
	return $tabcat;
}
