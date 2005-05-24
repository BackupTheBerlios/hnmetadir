<?PHP

include('HEADER.php');

// ##################################################################


$tpl->set_file('FileRef','index.html');




//$tpl->set_var('breves_bloc','breves');

$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
