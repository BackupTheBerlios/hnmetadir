<?

include('libs/class.template.inc');
include('libs/class.db.inc');
include('libs/class.users.inc');
include('functions.php'); // mes fonctions
include('fonctions.php'); // celles de vincent (php_inc)
include('config.inc.php');

// objet template
$tpl = new Template('templates/');

// connexion à mysql
$db  = new database;
$db->connect();

session_start();

// Untilisateur ou anonyme ?
if($_SESSION['auth'] != true && $REQUEST_URI != '/login.php') 
{
	if($popup == true) {
                echo '<script language="javascript">
                        window.opener.location.reload();
                        window.close();
                      </script>';
	} else {
		header('Location: login.php');
	}
}

$user = new user($_SESSION['auth_id'], $_SESSION['auth_login']);
