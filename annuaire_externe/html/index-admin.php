<?PHP

include('HEADER.php');

// on vérifie que le gas est bien admin
if( $_SESSION['auth_login'] != 'admin' )
{
	header('location: index.php');
}

// ##################################################################


$tpl->set_file('FileRef','index-admin.html');



$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
