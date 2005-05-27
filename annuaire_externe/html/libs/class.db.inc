<?PHP

class database {

		// ********************************
		var $HOST = 	"localhost";
		var $USER = 	"root";
		var $PASS = 	"";
		var $BDD  =	"annuaire_externe";
		// ********************************

		var $id_connect;
		var $select_db;
		var $result;
		var $error;
                var $requetes = 0;

	
	function connect () 
	{
		// Connexion	
		$this->id_connect = @mysql_connect($this->HOST, $this->USER, $this->PASS) or die("Trop de connexions simultannées. Serveur saturé.<br>Veuillez actualiser la page d'ici quelques instants.");

		if(!$this->id_connect) {
			$this->error_mysql("Impossible de se connecter à la base de données");
			return 0;
		}

		// Selection
		$this->select_db = @mysql_select_db($this->BDD, $this->id_connect);	

		if(!$this->select_db) {
			$this->error_mysql("Impossible d'acceder à la base de données");
			return 0;
		}
	}

	function change_db($new_db) {

        mysql_select_db($new_db, $this->id_connect); 
    }

	// Query avec la base
	function query($QUERY)
	{
		$this->result = mysql_query($QUERY, $this->id_connect);

		if(!$this->result) {
			$this->error_mysql("Problème de requète");
			return 0;
		}
		else return ($this->result);
	}


	// Fermeture de la base
	function close_mysql()
	{
		mysql_close($this->id_connect);
	}


	// Gestion des erreurs	
	function error_mysql($MSG) 
	{
		$this->error = @mysql_error($this->id_connect);
		
		echo "<B>Erreur :</B><BR><I>". $msg ."<BR> ". $this->error ."</I><BR>";
	}


	// Se positionne
	function fetch_array()
	{ 
 		return @mysql_fetch_array($this->result); 
	} 


	// Compte le nombre de tables
	function num_rows()
	{
		return @mysql_num_rows($this->result);
	}

}
?>
