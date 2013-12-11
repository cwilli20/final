<form action="edit.php"
      method="post"
      id="frmRegister">  
<?
$debug = false;
include("connect.php");
//make a query to get all the poets
$sql  = 'SELECT pkclientID, fldfirstName, fldlastName, fldphoneNum ';
$sql .= 'FROM tblclient ';
//$sql .= "WHERE pkclientID=" . $id;
$sql .= 'ORDER BY fldDateJoined';
if ($debug) print "<p>sql ". $sql;

$stmt = $db->prepare($sql);
            
$stmt->execute(); 

$poets = $stmt->fetchAll(); 
if($debug){ print "<pre>"; print_r($poets); print "</pre>";}

/*//****** new table select*******
 
//make a query to get all the poets
$sql  = 'SELECT fldpictures, fldcomment ';
$sql .= 'FROM tblmedia ';
//$sql .= "WHERE fkclientID=" . $id;
//$sql .= 'ORDER BY fldDateJoined';
if ($debug) print "<p>sql ". $sql;

$stmt = $db->prepare($sql);
            
$stmt->execute(); 

$poets = $stmt->fetchAll(); 
if($debug){ print "<pre>"; print_r($poets); print "</pre>";}
*/

include("top.php");
include("header.php");
include("menu.php");

// build list box
print '<fieldset class="listbox"><legend>Poets</legend><select name="lstPoets" size="1" tabindex="10">';

foreach ($poets as $poet) {
    print '<option value="' . $poet['pkclientID'] . '">' . $poet['fldfirstName'] . ' ' . $poet['fldlastName'] . "</option>\n";
}

print "</select>\n";

// build checkboxes
/*$tabIndex=20;
print '<fieldset class="checkbox"><legend>Poets</legend>';
foreach ($poets as $poet) {
    print '<label><input type="checkbox" ';
    print 'id="chk' . $poet['pkPoetID'] . '" ';
    print 'name="chk' . $poet['pkPoetID'] . '" ';
    print 'value="' . $poet['pkPoetID'] . '" ';
    print 'tabindex="' . $tabIndex++ . '">';
    print $poet['fldFname'] . ' ' . $poet['fldLastName'];
    print '</label>';
}

print '</fieldset>';
  */  

print "<input type='submit' name='cmdSubmitted' value='Submit' />";
print "</fieldset>\n";
print "</form>\n";