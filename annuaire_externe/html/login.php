<?PHP

session_start();

include('libs/class.db.inc');
include('libs/class.template.inc');
include('config.inc.php');
require('libs/class.nusoap.inc');
require('functions.php'); // necessaire pour fonction parcourant l'arbo

$tpl = new Template('templates/');

include('libs/class.users.inc');
$user = new user();

// ##################################################################

$tpl->set_file('FileRef','login.html');

if( $_GET['erreur'] == true )
{
	$tpl->set_var('erreur', 'Authentification échouée !<br>');
}


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

	$client = new soapclient($servldap_url.'auth.php');

	$rep = $user->auth($_POST['login'], $_POST['password']);
	
	if($rep == 'true') {
		header('Location: index.php');
	} else {
		header('Location: login.php?erreur=true');
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
