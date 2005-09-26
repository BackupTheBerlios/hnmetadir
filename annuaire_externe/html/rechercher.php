<?PHP

include('HEADER.php');

// ##################################################################

$tpl->set_file('FileRef','rechercher.html');


// RECHERCHE ENTITES
// ---------------------------------

$FCobj=new PYAobj();
$FCobj->NmTable='ENTITES';
$FCobj->NmBase=$DBName;
$nolig=0;

$db->query('SELECT `NM_CHAMP` from `DESC_TABLES` WHERE NM_TABLE="ENTITES" AND NM_CHAMP!="TABLE0COMM" AND (`NM_CHAMP`="CATEGORIES_CAT_ID" OR `NM_CHAMP`="ENT_RAISONSOCIAL" OR `NM_CHAMP`="ENT_NOMINATION" OR `NM_CHAMP`="ENT_CONAF" OR `NM_CHAMP`="ENT_VILLE" OR `NM_CHAMP`="ENT_CODEPOSTAL" OR `NM_CHAMP`="ENT_PAYS" OR `NM_CHAMP`="ENT_REGION" OR `NM_CHAMP`="ENT_MOTCLES") ORDER BY ORDAFF, LIBELLE');

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

$tpl->set_var('formulaire_entites', $tmp);

// RECHERCHE PERSONNES
// --------------------------------


unset($tmp,$FCobj,$res);
$FCobj=new PYAobj();
$FCobj->NmTable='PERSONNES';
$FCobj->NmBase=$DBName;

$db->query('SELECT `NM_CHAMP` from `DESC_TABLES` WHERE NM_TABLE="PERSONNES" AND NM_CHAMP!="TABLE0COMM" AND ( `NM_CHAMP`="PER_TITRE" OR `NM_CHAMP`="PER_NOM" OR `NM_CHAMP`="PER_PRENOM" OR `NM_CHAMP`="PER_VILLE" OR `NM_CHAMP`="PER_CODEPOSTAL" OR `NM_CHAMP`="PER_PAYS" OR `NM_CHAMP`="PER_REGION" ) ORDER BY ORDAFF, LIBELLE');

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
