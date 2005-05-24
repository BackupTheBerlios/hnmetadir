<?php

// On traite le haut de la page 
// ------------------------------------------------------------
$tpl->set_file('HeaderRef','header.html');

  $tpl->set_var('login', $_SESSION['auth_login']);

$tpl->parse('HeaderOut', 'HeaderRef');
// ------------------------------------------------------------

// On imprime la page dans le bonne ordre
$tpl->p('HeaderOut');
$tpl->p('FileOut');


// on ferme la connexion
$db->close_mysql();

