<?

include('libs/class.template.inc');
include('libs/class.db.inc');
include('functions.php');
include('fonctions.php');

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


### PROVISOIR
$_SESSION['auth_id']=11;
$_SESSION['auth_login']='admin';
