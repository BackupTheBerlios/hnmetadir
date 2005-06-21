<?PHP

session_start();

include('libs/class.db.inc');
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
elseif($_GET['type'] == 'user') 
{
	
	// connexion à mysql
	$db  = new database;
	$db->connect();

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
