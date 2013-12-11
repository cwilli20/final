<?php
$debug=false;

//############################################################################
//
// This page lists your tables and fields within your database. if you click on
// a database name it will show you all the records for that table.
// 
// You need to have the include file sent to the system admins.
// You need to change:
//username REDACTED
//                   
// the rest of the code can be left as it is. 
//############################################################################



// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^% 
//password REDACTED
// see: https://webdb.uvm.edu/web_publishing.php
//username REDACTED
//password REDACTED
//          
//password REDACTED
//          be given write or admin privleges.
//          
//          add to the example file the variables for your writer and admin 
//password REDACTED
//
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^% 

//username REDACTED
require_once("connect.php"); 
$dbName='CWILLI20_final';


?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>CS 148 Tables</title>
<meta charset="utf-8">
<meta name="author" content="Bob">
<meta name="description" content="Shows us a readable version of your database">
<style type="text/css">
    
aside{
    height: 200px; 
    float: left; 
    overflow: auto; 
    margin-left: 2em;            
}
section{
    width: 35%; 
    height: 200px; 
    float: left; 
    overflow: auto;
    margin-top:10%;
    margin-left:-24%;
}
table{
    border: medium #000080 solid;
    border-collapse: collapse;
}

td {
    border: thin #000080 solid;
    border-collapse: collapse;
}

.odd{
    background-color: lightcyan;
}

.even{
    background-color: whitesmoke;
}

</style>

<!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/sin/trunk/html5.js"></script>
<![endif]-->
    
</head>


<?php

include ("top.php"); 

include ("header.php"); 
include ("menu.php"); 
    
//<body id="nameOfFile">
$ext = pathinfo(basename($_SERVER['PHP_SELF']));
$file_name =  basename($_SERVER['PHP_SELF'],'.'.$ext['extension']);
print '<body id="' . $file_name . '">';

// create the PDO object
try { 
    
    $dsn='mysql:host=webdb.uvm.edu;dbname=';
    
/*password REDACTED
username REDACTED
password REDACTED
    
    
password REDACTED
    */
    if($debug) echo '<p>A You are connected to the database!</p>';
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    if($debug) echo "<p>A An error occurred while connecting to the database: $error_message </p>";
}

$tableName="";

if(isset($_GET['getRecordsFor'])){
    // Sanitize the input to help prevent sql injection
    $tableName=  htmlentities($_GET['getRecordsFor'], ENT_QUOTES);
}


print "<h2>Database: " .  $dbName . "</h2>";

// print out a list of all the tables and their description
// make each table name a link to display the record
print "<section id='tables'>";
//print "<p>Total tables: " . $db->numTables($databaseName);

$tableNameName = "Tables_in_" . $databaseName;
print "<table>";

$sql = "SHOW TABLES";
$rst = $db->prepare($sql);
$rst->execute();

foreach($rst as $row){

    // table name link
    print '<tr class="odd">';
    echo "<th colspan='6' style='text-align: left'><a href='?getRecordsFor=" . $row[0] ."#" . $row[0] . "'>" . $row[0] . "</a></th></tr>";
    //get the fields and any information about them
    

    $sql = "SHOW COLUMNS FROM " . $row[0];
    $rst2 = $db->prepare($sql);
    $rst2->execute();

    foreach($rst2 as $row2){
        print "<tr>";
        print "<td>" . $row2['Field'] . "</td>";
        print "<td>" . $row2['Type'] . "</td>";
        print "<td>" . $row2['Null'] . "</td>";
        print "<td>" . $row2['Key'] . "</td>";
        print "<td>" . $row2['Default'] . "</td>";
        print "<td>" . $row2['Extra'] . "</td>";
        print "</tr>";
    }
}
print "</table></section>";

if($tableName!=""){
    print "<aside id='records'>";

    $sql = "SHOW COLUMNS FROM " . $tableName;
        $info = $db->prepare($sql);
        $info->execute();
        
    $span = $info->rowCount();

    //print out the table name and how many records there are
    print "<table border='1'>";
    echo "<tr>";
        
        echo "<th colspan='" . $span . "' style='text-align: left'>" . $tableName;

    $sql = "SELECT * FROM " . $tableName;
        $a = $db->prepare($sql);
        $a->execute();
        
 
    echo " " . $a->rowCount($a). " records";

    echo "</th></tr>";
    
    //print out the column headings
    print "<tr>";
    $columns=0;
        foreach($info as $field){
            // ok messes up the pk since its not a 3 letter prefix. oh well
            print "<td>";
            $camelCase = preg_split('/(?=[A-Z])/',substr($field[0],3));
        
            foreach ($camelCase as $one){
                 print $one . " ";
            }
            
            "</td>";
            $columns++;
    }
    print "</tr><tr>";
    
    //now print out each record
    $sql = "SELECT * FROM " . $tableName;
        
        $info2 = $db->prepare($sql);
        $info2->execute();
        
        $highlight=0; // used to highlight alternate rows
        foreach($info2 as $rec){
            $highlight++;
            if ($highlight % 2 != 0){
            $style=" odd ";
        }else{
            $style = " even ";    
        }
        print '<tr class="' . $style . '">';
        for($i=0; $i<$columns;$i++){
            print "<td>" . $rec[$i]  . "</td>";
        }
        print "</tr>";
    }
    
    // all done
    print "</table>";
    print "</aside>";
}

?>
</div>
</body>
</html>