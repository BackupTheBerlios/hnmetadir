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
	function auth($login, $passwd, $anonyme=false) 
	{
		global $db, $_SESSION, $client;

		if($anonyme == true)
		{
			$this->id = '';
			$this->login = 'anonyme';
			$_SESSION['auth'] = true;
			$_SESSION['auth_id'] = $this->id;
			$_SESSION['auth_login'] = $this->login;
			return true;
		}
		elseif($login == 'admin' && $passwd == 'admin') 
		{
                        $this->id = '1';
                        $this->login = 'admin';
                        $_SESSION['auth'] = true;
                        $_SESSION['auth_id'] = $this->id;
			$_SESSION['auth_login'] = $this->login;
			return true;
		}
		else
		{
			// Authentification LDAP
			// Si auth ok : return true, sinon false.
/*
			$parametres['uid']=$login;
			$parametres['passwd']=$passwd; // l'encryption est faite dans le service
			$parametres['code_appli']="webcal"; // n'est PAS facultatif
			//$parametres[encrypt]=false; // facultatif
			
			$ldapauth = $client->call('auth', $parametres);			
			
			if ( $ldapauth['ConnOk'] ) 
			{
				$ret = true; // found login/password
				$ldapauth['Nom']=addslashes($ldapauth['Nom']);
				$ldapauth['Prenom']=addslashes($ldapauth['Prenom']);
				
				$db->query('SELECT * FROM `USERS` WHERE `USE_LOGIN`="'.$login.'"');
				
				// il y a t'il l'enregistrement ?
				if( $db->num_rows() )
				{
					$row = $db->fetch_array();
					
					// on met les champs à jour si besoin
					if( $row['USE_PRENOM'] != $ldapauth['Prenom'] OR $row['USE_NOM'] != $ldapauth['Nom'] )
					{
						$db->query('UPDATE `USERS` SET `USE_NOM`="'.$ldapauth['Nom'].'", `USE_PRENOM`="'.$ldapauth['Prenom'].'" WHERE `USE_ID`="'.$row['USE_ID'].'"');
					}
					
					$id = $row['USE_ID'];
				}
				else
				{
					$db->query('INSERT INTO `USERS` (USE_LOGIN,USE_NOM,USE_PRENOM) VALUES ("'.$login.'","'.$ldapauth['Nom'].'","'.$ldapauth['Prenom'].'")');
					$id = mysql_insert_id();
				}

				
				$this->id = $id;
				$this->login = $login;
				$_SESSION['auth'] = true;
				$_SESSION['auth_id'] = $this->id;
				$_SESSION['auth_login'] = $this->login;
			
				$_SESSION['user_groups'] = $this->GetHisGroups(); // on recupere ses groupes
				$_SESSION['user_cats'] = $this->GetHisCats(); // maintenant qu'on a ses groupes on peut savoir ou il à accès
				return true;
			}
			else
			{
				return false;
			}
*/
				$this->id = 1274;
				$this->login = fbreuill;
				$_SESSION['auth'] = true;
				$_SESSION['auth_id'] = $this->id;
				$_SESSION['auth_login'] = $this->login;
			
				$_SESSION['user_groups'] = $this->GetHisGroups();
				$_SESSION['user_cats'] = $this->GetHisCats(); 
                                return true;
			
		}
	}
	
	// - RECUPERATION DES GROUPES DANS LESQUELS IL EST 
	// --------------------------------------------------------------------------
	function GetHisGroups() 
	{
		global $db, $_SESSION;
		
		$tmp = array();
		$db->query('SELECT `GROUPES_GRO_ID` FROM `AFFECTE_USERS_GROUPES` WHERE `USERS_USE_ID`="'.$this->id.'"');
		while( $rows = $db->fetch_row() )
		{
			array_push($tmp, $rows[0]);
		}
		return $tmp;
	}

	// - RECUPERE LES IDs DES CATEGORIES OU IL A UN DROIT QUELCONQUE
	// ---------------------------------------------------------------------------------------------------
	function GetHisCats() 
	{
		global $_SESSION, $db, $tabcat;
		
		// on construit le where pour la requete
		for( $i=0; $i<count($_SESSION['user_groups']); $i++ )
		{
			$where .= ' `GROUPES_GRO_ID`="'.$_SESSION['user_groups'][$i].'" OR';
		}
		
		$where = substr( $where, 0, strlen($where)-2 );  // on vire le dernier 'OR'
		if(!$where) $where = 1;

                $tabcat = array();

		$db->query('SELECT * FROM `PERMISSIONS` WHERE '.$where);
		while( $rows = $db->fetch_array() )
		{
			GetSubCats( $rows['CATEGORIES_CAT_ID'], $rows['PERM_TYPE']); 
		}

		$db->query('SELECT `CAT_ID` FROM `CATEGORIES` WHERE `CAT_ADMIN`="'.$_SESSION['auth_id'].'"');
		while( $rows = $db->fetch_array() )
		{;
			GetSubCats( $rows['CAT_ID'], 'A'); 
		}

		return $tabcat;
	}

	// - PARCOURS LE TABLEAU AFIN DE SAVOIR SI L'USER A ACCES 
	// --------------------------------------------------------------------------------------
	function HaveAccess($id, $perm) 
	{
		global $_SESSION, $db;
		
		if( $_SESSION['auth_login'] == 'anonyme' )
		{
			return false;
		}
		elseif( $_SESSION['auth_login'] == 'admin' )
		{
			return true;
		}
		else
		{
       			// on veréifie qu'il existe une permition pour cette cat
       	       		for($i=0; $i<count($_SESSION['user_cats']); $i++)
              		{
       	       			if($id == $_SESSION['user_cats'][$i]['id'] && ereg($perm, $_SESSION['user_cats'][$i]['perm']) ) {
                                        return true;
       				}
       			} 
		} // fin si anonyme
	}


}
