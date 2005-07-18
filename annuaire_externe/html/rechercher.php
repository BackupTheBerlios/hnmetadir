<?PHP

include('HEADER.php');

// ##################################################################

$tpl->set_file('FileRef','rechercher.html');


// RECHERCHE ENTITEES
// ---------------------------------

$FCobj=new PYAobj();
$FCobj->NmTable='ENTITEES';
$FCobj->NmBase=$DBName;
$nolig=0;

$db->query('SELECT `NM_CHAMP` from `DESC_TABLES` WHERE NM_TABLE="ENTITEES" AND NM_CHAMP!="TABLE0COMM" AND (`NM_CHAMP`="CATEGORIES_CAT_ID" OR `NM_CHAMP`="ENT_PARENTID" OR `NM_CHAMP`="ENT_RAISONSOCIAL" OR `NM_CHAMP`="ENT_NOMINATION" OR `NM_CHAMP`="ENT_SIRET" OR `NM_CHAMP`="ENT_CONAF" OR `NM_CHAMP`="ENT_ADRESSE" OR `NM_CHAMP`="ENT_ADRESSE_COMP" OR `NM_CHAMP`="ENT_VILLE" OR `NM_CHAMP`="ENT_CODEPOSTAL" OR `NM_CHAMP`="ENT_PAYS" OR `NM_CHAMP`="ENT_REGION" OR `NM_CHAMP`="ENT_TEL" OR `NM_CHAMP`="ENT_MAIL" OR `NM_CHAMP`="ENT_SITEWEB" OR `NM_CHAMP`="ENT_DESCRIPTION" OR `NM_CHAMP`="ENT_PLANACCES" OR `NM_CHAMP`="ENT_MOTCLES") ORDER BY ORDAFF, LIBELLE');

while ($res = $db->fetch_array())
{
  $nolig++;
  $FCobj->NmChamp=$res['NM_CHAMP'];
  $FCobj->InitPO();
  $FCobj->DirEcho = false;
  $tmp .= "<TR><TD><B>$FCobj->Libelle</B><BR></TD><TD>";
  $tmp .= $FCobj->EchoFilt(false);
  $tmp .= "</TD></TR>";
}

$tpl->set_var('formulaire_entitees', $tmp);

// RECHERCHE PERSONNES
// --------------------------------


unset($tmp,$FCobj,$res);
$FCobj=new PYAobj();
$FCobj->NmTable='AFFECTE_ENTITEES_PERSONNES';
$FCobj->NmBase=$DBName;

$db->query('SELECT `NM_CHAMP` FROM `DESC_TABLES` WHERE `NM_TABLE`="AFFECTE_ENTITEES_PERSONNES" AND `NM_CHAMP`!="TABLE0COMM" AND (`NM_CHAMP`="ENTITEES_ENT_ID" OR `NM_CHAMP`="AEP_FONCTION" ) ORDER BY ORDAFF, LIBELLE');


while ($res2 = $db->fetch_array())
{
  $FCobj->NmChamp=$res2['NM_CHAMP'];
  $FCobj->InitPO();
  $FCobj->DirEcho = false;
  $tmp .= "<TR><TD><B>$FCobj->Libelle</B><BR></TD><TD>";
  $tmp .= $FCobj->EchoFilt(false);
  $tmp .= "</TD></TR>";
}


unset($FCobj,$res);
$FCobj=new PYAobj();
$FCobj->NmTable='PERSONNES';
$FCobj->NmBase=$DBName;

$db->query('SELECT `NM_CHAMP` from `DESC_TABLES` WHERE NM_TABLE="PERSONNES" AND NM_CHAMP!="TABLE0COMM" AND (`NM_CHAMP`="PER_IDLDAP" OR `NM_CHAMP`="PER_TITRE" OR `NM_CHAMP`="PER_NOM" OR `NM_CHAMP`="PER_PRENOM" OR `NM_CHAMP`="PER_ADRESSE" OR `NM_CHAMP`="PER_ADRESSE2" OR `NM_CHAMP`="PER_VILLE" OR `NM_CHAMP`="PER_CODEPOSTAL" OR `NM_CHAMP`="PER_PAYS" OR `NM_CHAMP`="PER_REGION" OR `NM_CHAMP`="PER_DATENAISS" OR `NM_CHAMP`="PER_SITEPERSO" ) ORDER BY ORDAFF, LIBELLE');

while ($res = $db->fetch_array())
{
  $FCobj->NmChamp=$res['NM_CHAMP'];
  $FCobj->InitPO();
  $FCobj->DirEcho = false;
  $tmp .= "<TR><TD><B>$FCobj->Libelle</B><BR></TD><TD>";
  $tmp .= $FCobj->EchoFilt(false);
  $tmp .= "</TD></TR>";
}


$tpl->set_var('formulaire_personnes', $tmp);


$tpl->parse('FileOut', 'FileRef');

// ######################################################################

include('FOOTER.php');
?>
