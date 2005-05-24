<?

include('libs/class.template.inc');
include('libs/class.db.inc');
include('functions.php');

// objet template
$tpl = new Template('templates/');

// connexion à mysql
$db  = new database;
$db->connect();

session_start();

// Untilisateur ou anonyme ?
if($_SESSION['auth'] != true && $REQUEST_URI != '/login.php' ) 
{
	header('Location: login.php');
}

// A déhà choisie une branche ? sinon hop index
if( !$_SESSION['branche_id'] && $REQUEST_URI != '/index.php' )
{
#	header('Location: index.php');
}


### PROVISOIR
$_SESSION['auth_id']=11;
$_SESSION['auth_login']='admin';


//$_SESSION['branche_id'] = '';
//$_SESSION['branche_nom'] = '';
//$_SESSION['branche_admin'] = '';
