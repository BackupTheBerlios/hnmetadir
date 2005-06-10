<?PHP

include('HEADER.php');

// ##################################################################


$tpl->set_file('FileRef','index.html');


header('Location: consulter.php');


$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
