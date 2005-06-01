<?php

// On traite le haut de la page 
// ------------------------------------------------------------
$tpl->set_file('HeaderRef','header.html');

	// affiche le login dans la barre grise
	$tpl->set_var('login', $_SESSION['auth_login']);


	// connexion ou déconnexion dans le sous meun
	if( $_SESSION['auth'] == true || $_SESSION['auth_login'] != 'anonyme')
	{
  		$tpl->set_var('connect', 'Déconnexion');
	}
	else
	{
		$tpl->set_var('connect', 'Connexion');
	}
	
$tpl->parse('HeaderOut', 'HeaderRef');
// ------------------------------------------------------------

// On imprime la page dans le bonne ordre
if($popup != true) 
{	
	$tpl->p('HeaderOut');
	$tpl->p('FileOut');
}

// on ferme la connexion
$db->close_mysql();

