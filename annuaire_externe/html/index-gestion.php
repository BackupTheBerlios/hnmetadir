<?PHP

include('HEADER.php');

if( ($_SESSION['branche_admin'] != true) || ($_SESSION['auth_login'] != 'admin') && !$_SESSION['branche_id'] ) 
{
	header('location: index.php');
}


// ##################################################################

$tpl->set_file('FileRef','index-gestion.html');



$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
