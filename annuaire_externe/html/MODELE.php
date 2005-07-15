<?PHP

include('HEADER.php');

// ##################################################################

$tpl->set_file('FileRef','index.html');





$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
