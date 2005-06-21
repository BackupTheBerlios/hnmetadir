<?php

class user {

	var $id;
	var $login;

	function user($id='', $login='')
	{
		if($id) {
			$this->id = $id;
		}
		
		if($login) {
			$this->login = $login;
		}
	}

	// - AUTHENTIFICATION
	// -----------------------------------------------------------------------
	function auth($login, $pass, $anonyme=false) 
	{
		global $db, $_SESSION;

		if($anonyme == true)
		{
			$this->id = '';
			$this->login = 'anonyme';
			$_SESSION['auth'] = true;
			$_SESSION['auth_id'] = $this->id;
			$_SESSION['auth_login'] = $this->login;
			return true;
		}
		else
		{
			// authentification LDAP
			// si auth ok : return true, sinon false.
			
			// bidouille
			$this->id=11;
			$this->login='admin';
			$_SESSION['auth'] = true;
			$_SESSION['auth_id'] = $this->id;
			$_SESSION['auth_login'] = $this->login;			
			// fin de la bidouille
			
			$_SESSION['user_groups'] = $this->GetHisGroups(); // on recupere ses groupes
			$_SESSION['user_cats'] = $this->GetHisCats(); // maintenant qu'on a ses groupes on peut savoir ou il à accès
			return true;
		}
	}
	
	// - RECUPERATION DES GROUPES DANS LESQUELS IL EST 
	// --------------------------------------------------------------------------
	function GetHisGroups() 
	{
		global $db, $_SESSION;
		
		$db->query('SELECT `GROUPES_GRO_ID` FROM `AFFECTE_USERS_GROUPES` WHERE `USERS_USE_ID`="'.$this->id.'"');
		$rows = $db->fetch_array();
		return $rows;
	}

	function GetHisCats() 
	{
	}

	// - PARCOURS LE TABLEAU AFIN DE SAVOIR SI L'USER A ACCES 
	// --------------------------------------------------------------------------------------
	function HaveAccess($id, $perm) 
	{
		global $_SESSION;
		
		if( $_SESSION['auth_login'] == 'anonyme' )
		{
			return false;
		}
		else
		{
			$found = false;
			for($i=0; $i<count($_SESSION['his']); $i++)
			{
				if($id == $_SESSION['hiscat'][$i]) $found=true;
			} 
			return $found;
		}
	}


}
