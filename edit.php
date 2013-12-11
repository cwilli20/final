<?php
/* the purpose of this page is to display a form to allow a person to either
 * add a new record if not pk was passed in or to update a record if a pk was
 * passed in.
 * 
 * notice i have more than one submit button on the form and i need to make
 * sure they have different names
 * 
 * Written By: Robert Erickson robert.erickson@uvm.edu
 * Last updated on: November 5, 2013
 * 
 * 
 -- --------------------------------------------------------

    --
    -- Table structure for table `tblPoet`
    --

    CREATE TABLE IF NOT EXISTS `tblPoet` (
      `pkPoetId` int(11) NOT NULL AUTO_INCREMENT,
      `fldFname` varchar(20) DEFAULT NULL,
      `fldLastName` varchar(20) DEFAULT NULL,
      `fldBirthDate` date DEFAULT NULL,
      PRIMARY KEY (`pkPoetId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

 */


//-----------------------------------------------------------------------------
// 
// Initialize variables
//  


$debug = false;
if (isset($_GET["debug"])) {
    $debug = false;
}

include("connect.php");

$baseURL = "http://www.uvm.edu/~cwilli20/"; 
$folderPath = "cs148/assignment7.1/";
// full URL of this form
$yourURL = $baseURL . $folderPath . "edit.php";

$fromPage = getenv("http_referer");

if ($debug) {
    print "<p>From: " . $fromPage . " should match ";
    print "<p>Your: " . $yourURL;
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// initialize my form variables either to waht is in table or the default 
// values.
// display record to update
if (isset($_POST["lstPoets"])) {
    

    // you may want to add another security check to make sure the person
    // is allowed to delete records.
    
    $id = htmlentities($_POST["lstPoets"], ENT_QUOTES);
	
    $sql = "SELECT fldfirstName, fldlastName, fldphoneNum, fldEmail ";
    $sql .= "FROM tblclient ";
    $sql .= "WHERE pkclientID=" . $id;

    if ($debug)
        print "<p>sql " . $sql;
        //print $id

    $stmt = $db->prepare($sql);

    $stmt->execute();

    $poets = $stmt->fetchAll();
    if ($debug) {
        print "<pre>";
        print_r($poets);
        print "</pre>";
    }

    foreach ($poets as $poet) {
        $firstName = $poet["fldfirstName"];
        $lastName = $poet["fldlastName"];
        $phone = $poet["fldphoneNum"];
        $email = $poet["fldEmail"];
    }
    //SET fldfirstName="' . $firstname . '",fldlastName="' . $lastname . '",fldEmail="' . $email . '",fldphoneNum="' . $phone . '"';
} else { //defualt values

    $id = "";
    $firstName = "";
    $lastName = "";
    $phone = "";
    $email = "";
   } 
   
    //********************* new table passing to admin form*****************
  if (isset($_POST["lstPoets"])) {
$id = htmlentities($_POST["lstPoets"], ENT_QUOTES);

    $sql = "SELECT fldpictures, fldcomment ";
    $sql .= "FROM tblmedia ";
    $sql .= "WHERE fkclientID=" . $id;

    if ($debug)
        print "<p>sql " . $sql;

    $stmt = $db->prepare($sql);

    $stmt->execute();

    $poets = $stmt->fetchAll();
    if ($debug) {
        print "<pre>";
        print_r($poets);
        print "</pre>";
    }

    foreach ($poets as $poet) {
    	
        $file = $poet["fldpictures"];
        $comment = $poet["fldcomment"];
        print $file; 
    }
    //SET fldfirstName="' . $firstname . '",fldlastName="' . $lastname . '",fldEmail="' . $email . '",fldphoneNum="' . $phone . '"';
} else { //defualt values
	$id = "";
    $file = "";
    $comment = "";
    


} // end isset lstPoets

//******** new table select *******
/*
if (isset($_POST["lstPoets"])) {
$id = htmlentities($_POST["lstPoets"], ENT_QUOTES);

    $sql = "SELECT fkemail, fkphoneNum, fkfirstName, fklastName, fkpictures ";
    $sql .= "FROM tblmedia ";
    $sql .= "WHERE fkclientID=" . $id;
    if ($debug)
        print "<p>sql " . $sql;

    $stmt = $db->prepare($sql);

    $stmt->execute();

    $poets = $stmt->fetchAll();
    if ($debug) {
        print "<pre>";
        print_r($poets);
        print "</pre>";
    }

    foreach ($poets as $poet) {
    	
        $email = $poet["fkemail"];
        $phone = $poet["fkphoneNum"];
        $firstname = $poet["fkfirstName"];
        $lastname = $poet["fklastName"];
        $file = $poet["fkpictures"];
    }
    //SET fldfirstName="' . $firstname . '",fldlastName="' . $lastname . '",fldEmail="' . $email . '",fldphoneNum="' . $phone . '"';
} else { //defualt values
	    $email = "";
        $phone = "";
        $firstname = "";
        $lastname = "";
        $file = "";
    


} // end isset lstPoets
*/
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// simple deleting record. 
if (isset($_POST["cmdDelete"])) {
//-----------------------------------------------------------------------------
// 
// Checking to see if the form's been submitted. if not we just skip this whole 
// section and display the form
// 
//#############################################################################
// minor security check
    if ($fromPage != $yourURL) {
        die("<p>Sorry you cannot access this page. Security breach detected and reported.</p>");
    }

    // you may want to add another security check to make sure the person
    // is allowed to delete records.
    
    $delId = htmlentities($_POST["deleteId"], ENT_QUOTES);

    // I may need to do a select to see if there are any related records.
    // and determine my processing steps before i try to code.
    
    //Delete first fk
	$sql = "DELETE ";
	$sql .= "FROM tblwebSite ";
    $sql .= "WHERE fkclientID=" . $delId;

    
    if ($debug)
        print "<p>sql " . $sql;

    $stmt = $db->prepare($sql);

    $DeleteData = $stmt->execute();

    //delete second fk
    $sql = "DELETE ";
    $sql .= "FROM tblmedia ";
    $sql .= "WHERE fkclientID=" . $delId;
    
    if ($debug)
        print "<p>sql " . $sql;

    $stmt = $db->prepare($sql);

    $DeleteData = $stmt->execute();
    
    //delete pk
    $sql = "DELETE ";
    $sql .= "FROM tblclient ";
    $sql .= "WHERE pkclientID=" . $delId;
    
    if ($debug)
        print "<p>sql " . $sql;

    $stmt = $db->prepare($sql);

    $DeleteData = $stmt->execute();
    // at this point you may or may not want to redisplay the form
  /*  if($DeleteData){
        header('Location: delete.php');
        exit();
    }*/
}

//-----------------------------------------------------------------------------
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// if form has been submitted, validate the information both add and update
if (isset($_POST["btnSubmitted"])) {
    if ($fromPage != $yourURL) {
        die("<p>Sorry you cannot access this page. Security breach detected and reported.</p>");
    }
    
    // initialize my variables to the forms posting	
    $id = htmlentities($_POST["id"], ENT_QUOTES);
    $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES);
    $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES);
    $phone = htmlentities($_POST["txtPhoneNum"], ENT_QUOTES);
    $email = htmlentities($_POST["txtEmail"], ENT_QUOTES, "UTF-8");
    $file = htmlentities($_FILES["imgFile"]["name"], ENT_QUOTES, "UTF-8");
    $comment = htmlentities($_POST["comment"], ENT_QUOTES, "UTF-8");
	print $file;
    
    // Error checking forms input
    include ("validation.php");

    $errorMsg = array();

    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    // begin testing each form element 
    if ($firstName == "") {
        $errorMsg[] = "Please enter your First Name";
    } else {
        $valid = verifyAlphaNum($firstName); /* test for non-valid  data */
        if (!$valid) {
            $error_msg[] = "First Name must be letters and numbers, spaces, dashes and ' only.";
        }
    }

    if ($lastName == "") {
        $errorMsg[] = "Please enter your Last Name";
    } else {
        $valid = verifyAlphaNum($lastName); /* test for non-valid  data */
        if (!$valid) {
            $error_msg[] = "Last Name must be letters and numbers, spaces, dashes and ' only.";
        }
    }
 if ($phone == "") {
        $errorMsg[] = "Please enter your phone number";
    } else {
        $valid = verifyPhone($lastName); /* test for non-valid  data */
        if (!$valid) {
            $error_msg[] = "Last Name must be letters and numbers, spaces, dashes and ' only.";
        }
    }
    if ($email == "") {
        $errorMsg[] = "Please enter your email";
    } else {
        $valid = verifyEmail($lastName); /* test for non-valid  data */
        if (!$valid) {
            $error_msg[] = "Last Name must be letters and numbers, spaces, dashes and ' only.";
        }
    }

    //- end testing ---------------------------------------------------
/*    if (isset($_POST["id"])) { // update record
            $sql = "UPDATE ";
            $sql .= "tblmedia SET ";
            $sql .= "fldpictures='$file',";
            $sql .= "fkemail='$email',";
            $sql .= "fkclientID='$primaryKey1',";
            $sql .= "fldcomment='$comment'";
        } else { // insert record
            $sql = "INSERT INTO ";
            $sql .= "tblmedia SET ";
            $sql .= "fldpictures='$file',";
            $sql .= "fkemail='$email',";
            $sql .= "fkclientID='$primaryKey1',";
            $sql .= "fldcomment='$comment'";
        }*/
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    // there are no input errors so form is valid now we need to save 
    // the information checking to see if it is an update or insert
    // query based on the hidden html input for id
    if (!$errorMsg) {
        
        if ($debug)
            echo "<p>Form is valid</p>";

        if (isset($_POST["id"])) { // update record
            $sql = "UPDATE ";
            $sql .= "tblclient SET ";
            $sql .= "fldfirstName='$firstName',";
            $sql .= "fldlastName='$lastName',";
            $sql .= "fldphoneNum='$phone',";
            $sql .= "fldEmail='$email' ";
            $sql .= "WHERE pkclientID=" . $id;
        } else { // insert record
            $sql = "INSERT INTO ";
            $sql .= "tblclient SET ";
            $sql .= "fldfirstName='$firstName', ";
            $sql .= "fldlastName='$lastName', ";
            $sql .= "fldphoneNum='$phone', ";
            $sql .= "fldEmail='$email'";
        }
        

        if ($debug)
            echo "<p>SQL: " . $sql . "</p>";

        $stmt = $db->prepare($sql);

        $enterData = $stmt->execute();
		//$primaryKey1 = $db->lastInsertId();
        // Processing for other tables falls into place here. I like to use
        // the same variable $sql so i would repeat above code as needed.
    if ($debug){
            print "<p>Record has been updated";
        }

        
        if ($debug)
            echo "<p>Form is valid</p>";

        if (isset($_POST["id"])) { // update record
            $sql = "UPDATE ";
            $sql .= "tblmedia SET ";
            $sql .= "fldpictures='$file', ";
            $sql .= "fkemail='$email', ";
            $sql .= "fldcomment='$comment' ";
            $sql .= "WHERE fkclientID=" . $id;
        } else { // insert record
            $sql = "INSERT INTO ";
            $sql .= "tblmedia SET ";
            $sql .= "fldpictures='$file', ";
            $sql .= "fkemail='$email', ";
            $sql .= "fldcomment='$comment'";
        }
        

        if ($debug)
            echo "<p>SQL: " . $sql . "</p>";

        $stmt = $db->prepare($sql);

        $enterData = $stmt->execute();
		//$primaryKey2 = $db->lastInsertId();
        // Processing for other tables falls into place here. I like to use
        // the same variable $sql so i would repeat above code as needed.
    if ($debug){
            print "<p>Record has been updated";
        }

        
        if ($debug)
            echo "<p>Form is valid</p>";

        if (isset($_POST["id"])) { // update record
            $sql = "UPDATE ";
            $sql .= "tblwebSite SET ";
            $sql .= "fkemail='$email', ";
            $sql .= "fkphoneNum='$phone' ";
            $sql .= "WHERE fkclientID=" . $id;
        } else { // insert record
            $sql = "INSERT INTO ";
            $sql .= "tblwebSite SET ";
            $sql .= "fkemail='$email', ";
            $sql .= "fkphoneNum='$phone', ";

            
        }
        
//$sql = 'INSERT INTO tblwebSite SET fkclientID="' . $primaryKey1 . '",fkmediaID="' . $primaryKey2 . '",fkemail="' . $email . '",fkphoneNum="' . $phone . '",fkfirstName="' . $firstname . '",fklastName="' . $lastname . '",fkpictures="' . $file . '"';


        if ($debug)
            echo "<p>SQL: " . $sql . "</p>";

        $stmt = $db->prepare($sql);

        $enterData = $stmt->execute();
	
        if ($debug){
            print "<p>Record has been updated";
        }
        
        
    }// end no errors	
} // end isset cmdSubmitted
 
include("top.php");
include("header.php");
include("menu.php");

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// display any errors at top of form page
if ($errorMsg) {
    echo "<ul>\n";
    foreach ($errorMsg as $err) {
        echo "<li style='color: #ff6666'>" . $err . "</li>\n";
    }
    echo "</ul>\n";
} //- end of displaying errors ------------------------------------

if ($id != "") {
    print "<h1>Edit client Information</h1>";
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    // display a delete option
    ?>
    <form action="<? print $_SERVER['PHP_SELF']; ?>" method="post">
        <fieldset>
            <input type="submit" name="cmdDelete" value="Delete" />
            <?php print '<input name= "deleteId" type="hidden" id="deleteId" value="' . $id . '"/>'; ?>
        </fieldset>	
    </form>
    <?
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^% 
} else {
    print "<h1>Add Poet Information</h1>";
}
?>

<form action="edit.php" method="post">
    <fieldset>
        <label for="txtFirstName"> Name*</label><br>
        <input name="txtFirstName" type="text" size="20" id="txtFirstName" <? print "value='$firstName'"; ?>/><br>

        <label for="txtLastName">Last Name*</label><br>
        <input name="txtLastName" type="text" size="20" id="txtLastName" <? print "value='$lastName'"; ?>/><br>

        <label for="txtPhoneNum"> Number*</label><br>
        <input name="txtPhoneNum" type="text" size="20" id="txtPhoneNum" <? print "value='$phone'"; ?> /><br>
        
        <label for="txtEmail"> Email*</label><br>
        <input name="txtEmail" type="text" size="20" id="txtEmail" <? print "value='$email'"; ?> /><br>
        
        <label for="file">Filename:</label>
        <? print $file; ?>
<input type="file" name="imgFile" id="file"/><br>
<label for="comment">Enter any general comments or text for photos here</br></label>


<textarea rows="10" cols="60" name="comment"<? print "value='$comment'"; ?>/>
<? print $comment; ?>
</textarea>

        <?
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// if there is a record then we need to be able to pass the pk back to the page
        if ($id != "")
            print '<input name= "id" type="hidden" id="id" value="' . $id . '"/>';
        ?>
        <input type="submit" name="btnSubmitted" value="Submit" />
    </fieldset>		
</form>
<?php

include ("footer.php");
?>
</body>
</html>