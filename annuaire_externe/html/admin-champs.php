<?PHP

include('HEADER.php');

if( $_SESSION['auth_login'] != 'admin' ) header('Location: index.php');

// ##################################################################


$tpl->set_file('FileRef','admin-champs.html');





$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
