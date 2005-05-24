<?PHP

session_start();

include('libs/class.template.inc');
$tpl = new Template('templates/');

// ##################################################################

$tpl->set_file('FileRef','login.html');

if($type == 'anonyme')
{
	$_SESSION['auth'] = true;
	$_SESSION['auth_id']='';
	$_SESSION['auth_login']='anonyme';
	header('Location: index.php');

} 
elseif($type == 'user') 
{

} 
else 
{
	session_destroy();
}

$tpl->parse('FileOut', 'FileRef');

// ######################################################################

$tpl->p('FileOut');

?>
