<?PHP

session_start();

include('libs/class.template.inc');
$tpl = new Template('templates/');

include('libs/class.users.inc');
$user = new user();

// ##################################################################

$tpl->set_file('FileRef','login.html');

if($_GET['type'] == 'anonyme')
{
	$user->auth('','',true);
	header('Location: index.php');

} 
elseif($type == 'user') 
{
	$rep = $user->auth($_POST['login'], $_POST['password']);
	
	if($rep == true) {
		header('Location: index.php');
	} else {
		header('Location: login.php');
	}

} 
else 
{
	session_destroy();
}

$tpl->parse('FileOut', 'FileRef');

// ######################################################################

$tpl->p('FileOut');

?>
