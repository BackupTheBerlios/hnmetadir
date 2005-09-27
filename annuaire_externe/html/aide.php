<?PHP

include('HEADER.php');

// ##################################################################

$tpl->set_file('FileRef','aide.html');





$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
