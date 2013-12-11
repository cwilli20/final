<?php 

/* the purpose of this page is to display a form to allow a person to register 
 * the form will be sticky meaning if there is a mistake the data previously  
 * entered will be displayed again. Once a form is submitted (to this same page) 
 * we first sanitize our data by replacing html codes with the html character. 
 * then we check to see if the data is valid. if data is valid enter the data  
 * into the table and we send and dispplay a confirmation email message.  
 *  
 * if the data is incorrect we flag the errors. 
 *  
 * Written By: Connor Williams and Robert Erickson cwilli20@uvm.edu robert.erickson@uvm.edu 
 * Last updated on: October 10, 2013 
 *  
 *  
 * I am using a surrogate key for demonstration,  
 * email would make a good primary key as well which would prevent someone 
 * from entering an email address in more than one record. 
 */ 

//----------------------------------------------------------------------------- 
//  
// Initialize variables 
//   

$debug = false; 
if ($debug) print "<p>DEBUG MODE IS ON</p>"; 

$baseURL = "http://www.uvm.edu/~cwilli20/"; 
$folderPath = "cs148/assignment7.1/"; 
// full URL of this form 
$yourURL = $baseURL . $folderPath . "register.php"; 

require_once("connect.php"); 

//############################################################################# 


$firstname = "";
$lastname = "";
$email = "";
$phone = "";

//############################################################################# 
//  
// flags for errors 
$firstnameERROR = false;
$lastnameERROR = false;
$emailERROR = false;
//$radioERROR = false;



//############################################################################# 
//   
$mailed = false; 
$messageA = "What is message A for?"; 
$messageB = "What is message B for?"; 
$messageC = "Now make sure you update your adventures."; 


//----------------------------------------------------------------------------- 
//  
// Checking to see if the form's been submitted. if not we just skip this whole  
// section and display the form 
//  
//############################################################################# 
// minor security check 

if (isset($_POST["btnSubmit"])) { 
    $fromPage = getenv("http_referer"); 

    if ($debug) 
        //print "<p>From: " . $fromPage . " should match "; 
        //print "<p>Your: " . $yourURL; 

    if ($fromPage != $yourURL) { 
        die("<p>Sorry you cannot access this page. Security breach detected and reported.</p>"); 
    } 


//############################################################################# 
// replace any html or javascript code with html entities 
// 
	$firstname = htmlentities($_POST["firstname"], ENT_QUOTES, "UTF-8");
	$lastname = htmlentities($_POST["lastname"], ENT_QUOTES, "UTF-8");
    $email = htmlentities($_POST["txtEmail"], ENT_QUOTES, "UTF-8");
    $phone = htmlentities($_POST["txtPhone"], ENT_QUOTES, "UTF-8");
    $file = htmlentities($_FILES["imgFile"]["name"], ENT_QUOTES, "UTF-8");
    $comment = htmlentities($_POST["comment"], ENT_QUOTES, "UTF-8");
  

//############################################################################# 
//  
// Check for mistakes using validation functions 
// 
// create array to hold mistakes 
//  

    include ("validation.php"); 

    $errorMsg = array(); 


//############################################################################ 
//  
// Check each of the fields for errors then adding any mistakes to the array. 
// 
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^       Check email address 
    
     if (empty($firstname)) { 
        $errorMsg[] = "Please Enter Your First Name"; 
        $firstnameERROR = true; 
    } else { 
        $valid = verifyText($firstname); /* test for non-valid  data */ 
        if (!$valid) { 
            $errorMsg[] = "I'm sorry, the First Name you entered is not valid."; 
            $firstnameERROR = true; 
        } 
    } 
    
    if (empty($lastname)) { 
        $errorMsg[] = "Please Enter Your Last Name"; 
        $lastnameERROR = true; 
    } else { 
        $valid = verifyText($lastname); /* test for non-valid  data */ 
        if (!$valid) { 
            $errorMsg[] = "I'm sorry, the Last Name you entered is not valid."; 
            $lastnameERROR = true; 
        } 
    } 
    
    if (empty($email)) { 
        $errorMsg[] = "Please enter your Email Address"; 
        $emailERROR = true; 
    } else { 
        $valid = verifyEmail($email); /* test for non-valid  data */ 
        if (!$valid) { 
            $errorMsg[] = "I'm sorry, the email address you entered is not valid."; 
            $emailERROR = true; 
        } 
    } 
    
     if (empty($phone)) { 
        $errorMsg[] = "Please enter your Phone Number"; 
        $phoneERROR = true; 
    } else { 
        $valid = verifyPhone($phone); /* test for non-valid  data */ 
        if (!$valid) { 
            $errorMsg[] = "I'm sorry, the Phone Number you entered is not valid."; 
            $phoneERROR = true; 
        } 
    } 


//############################################################################ 
//  
// Processing the Data of the form 
// 

    if (!$errorMsg) { 
        if ($debug) print "<p>Form is valid</p>"; }

//############################################################################ 
// 
// the form is valid so now save the information 
//     

        $dataEntered = false;
        
        try {
            $db->beginTransaction();
           
            $sql = 'INSERT INTO tblclient SET fldfirstName="' . $firstname . '",fldlastName="' . $lastname . '",fldEmail="' . $email . '",fldphoneNum="' . $phone . '"';
            
            $stmt = $db->prepare($sql);
            if ($debug) print "<p>sql ". $sql;
       
            $stmt->execute();
            
            $primaryKey1 = $db->lastInsertId();
            if ($debug) print "<p>pk= " . $primaryKey1;

            // all sql statements are done so lets commit to our changes
            $dataEntered = $db->commit();
            if ($debug) print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $db->rollback();
            if ($debug) print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }
 
         try {
            $db->beginTransaction();
           
            $sql = 'INSERT INTO tblmedia SET fldpictures="' . $file . '",fkemail="' . $email . '",fkclientID="' . $primaryKey1 . '",fldcomment="' . $comment . '"';
            $stmt = $db->prepare($sql);
            if ($debug) print "<p>sql ". $sql;
       
            $stmt->execute();
            
            $primaryKey2 = $db->lastInsertId();
            if ($debug) print "<p>pk= " . $primaryKey2;

            // all sql statements are done so lets commit to our changes
            $dataEntered = $db->commit();
            if ($debug) print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $db->rollback();
            if ($debug) print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }
        
        try {
            $db->beginTransaction();
           
            $sql = 'INSERT INTO tblwebSite SET fkclientID="' . $primaryKey1 . '",fkmediaID="' . $primaryKey2 . '",fkemail="' . $email . '",fkphoneNum="' . $phone . '"';
            $stmt = $db->prepare($sql);
            if ($debug) print "<p>sql ". $sql;
       
            $stmt->execute();
            
           /* not needed for this relational storage table
            $primaryKey3 = $db->lastInsertId();
            if ($debug) print "<p>pk= " . $primaryKey3;
			*/
            // all sql statements are done so lets commit to our changes
            $dataEntered = $db->commit();
            if ($debug) print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $db->rollback();
            if ($debug) print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }
        
        
        
        // If the transaction was successful, give success message
        if ($dataEntered) {
            if ($debug) print "<p>data entered now prepare keys ";
            
            
            //################################################################# 
            // create a key value for confirmation 

            $sql = "SELECT fldDateJoined FROM tblclient WHERE pkclientID=" . $primaryKey1; 
            $stmt = $db->prepare($sql); 
            $stmt->execute(); 

            $result = $stmt->fetch(PDO::FETCH_ASSOC); 
             
            $dateSubmitted = $result["fldDateJoined"]; 

            $key1 = sha1($dateSubmitted); 
            $key2 = $primaryKey1; 

            //print "<p>key 1: " . $key1; 
            //print "<p>key 2: " . $key2; 

/*  copied code from: http://www.w3schools.com/php/php_file_upload.asp
 *  added my debug and if statements.
 * 
 *   create folder for form and set htaccess to only allow uvm people
 *   https://www.uvm.edu/htpasswd
 * 
 *   created subfolder images
 *   set properties for images to unix-> chmod 777 images
 * 
 *   changed code from upload to images
 * 
 *   tested it. worked.
 * 
 *   added if debug to w3schools  echo's
 * 
 *   create page to display images, put in protected folder and it works
 *   put one level up from protected folder does not work.
 * 
 */
$debug = false;
if (isset($_GET["debug"])) {
    $debug = false;
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// initialize my form variables either to waht is in table or the default 
// values.
// display record to update
if (isset($_POST["btnSubmit"])) {
    if ($fromPage != $yourURL) {
        die("<p>Sorry you cannot access this page. Security breach detected and reported.</p>");
    }
    
    /* code from w3schools */
    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $_FILES["imgFile"]["name"]);
    $extension = end($temp);
    
    if ((($_FILES["imgFile"]["type"] == "image/gif")
    || ($_FILES["imgFile"]["type"] == "image/jpeg")
    || ($_FILES["imgFile"]["type"] == "image/jpg")
    || ($_FILES["imgFile"]["type"] == "image/pjpeg")
    || ($_FILES["imgFile"]["type"] == "image/x-png")
    || ($_FILES["imgFile"]["type"] == "image/png"))
    && ($_FILES["imgFile"]["size"] < 2000000)
    && in_array($extension, $allowedExts))
      {
      if ($_FILES["file"]["error"] > 0) {
        if ($debug) { echo "Return Code: " . $_FILES["imgFile"]["error"] . "<br>";}
        $output="<p>There was a problem submitting your file</p>";
      } else {
        if ($debug) {
            echo "<p>Upload: " . $_FILES["imgFile"]["name"] . "<br>";
            echo "Type: " . $_FILES["imgFile"]["type"] . "<br>";
            echo "Size: " . ($_FILES["imgFile"]["size"] / 1024) . " kB<br>";
            echo "Temp file: " . $_FILES["imgFile"]["tmp_name"] . "<br>";
        }
        
        if (file_exists("photos/" . $_FILES["imgFile"]["name"])){
          $output= $_FILES["imgFile"]["name"] . " already exists. ";
        }else{
          move_uploaded_file($_FILES["imgFile"]["tmp_name"],"photos/" . $_POST["txtEmail"] . $_FILES["imgFile"]["name"]);
          $output="<p>File Stored in: " . "photos/" . $_POST["txtEmail"] . $_FILES["imgFile"]["name"];
          }
        }
      }
    else
      {
      $output="<p>Invalid file";
      }
   
            //################################################################# 
            // 
            //Put forms information into a variable to print on the screen 
            // 

            $messageA = '<h2>Thank you for registering.</h2>'; 

            $messageB = "<p>Click this link to confirm your registration: "; 
            $messageB .= '<a href="' . $baseURL . $folderPath  . 'confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . '">Confirm Registration</a></p>'; 
            $messageB .= "<p>or copy and paste this url into a web browser: "; 
            $messageB .= $baseURL . $folderPath  . 'confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . "</p>"; 

			$messageC .= "<p><b>First Name:</b><i>   " . $firstname . "</i></p>";
			$messageC .= "<p><b>Last Name:</b><i>   " . $lastname . "</i></p>";
            $messageC .= "<p><b>Email Address:</b><i>   " . $email . "</i></p>";
            $messageC .= "<p><b>Phone Number:</b><i>   " . $phone . "</i></p>";
            $messageC .= "<p><b>File Uploaded:</b><i>   " . $file . "</i></p>";
            $messageC .="<p><b>Type:</b><i> " . $_FILES["imgFile"]["type"] . "</i></p>";
            $messageC .="<p><b>Size:</b><i> " . ($_FILES["imgFile"]["size"] / 1024) . " kB</i></p>";
            $messageC .="<p><b>Temp file:</b><i> " . $_FILES["imgFile"]["tmp_name"] . "</i></p>";
            $messageC .= "<p><b>Comments:</b><i>   " . $comment . "</i></p>";
            
            /*$messageC .= "<p><b>Do you have Free Time?:</b><i>   " . $radio1 . "</i></p>";
            $messageC .= "<p><b>Do you like having dirty dishes in the apartment?:</b><i>   " . $radio2 . "</i></p>";
            $messageC .= "<p><b>What day(s) are you free?:</b><i>   " . $check0 . $check1 . $check2 . $check3 . $check4 . $check5 .$check6 ."</i></p>";
            $messageC .= "<p><b>How good will you be about doing your dishes?:</b><i>   " . $list1 . "</i></p>";
            */
        
            
            
            //print_r($_POST);

            //############################################################## 
            // 
            // email the form's information 
            // 
             
            $subject = "CS 148 registration"; 
            include_once('mailMessage.php'); 
            $mailed = sendMail($email, $subject, $messageA . $messageB . $messageC); 
        } //data entered    
    } // no errors  
}// ends if form was submitted.  

    include ("top.php"); 

    $ext = pathinfo(basename($_SERVER['PHP_SELF'])); 
    $file_name = basename($_SERVER['PHP_SELF'], '.' . $ext['extension']); 

    print '<body id="' . $file_name . '">'; 

    include ("header.php"); 
    include ("menu.php"); 
    ?> 

    <section id="main"> 
        <h1 id="formtitle">Register </h1> 

        <? 
//############################################################################ 
// 
//  In this block  display the information that was submitted and do not  
//  display the form. 
// 
        if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { 
            print "<h2>Your Request has "; 

            if (!$mailed) { 
                echo "not "; 
            } 

            echo "been processed</h2>"; 

            print "<p>A copy of this message has "; 
            if (!$mailed) { 
                echo "not "; 
            } 
            print "been sent to: " . $email . "</p>"; 

            echo $messageA . $messageC; 
        } else { 


//############################################################################# 
// 
// Here we display any errors that were on the form 
// 

            print '<div id="errors">'; 

            if ($errorMsg) { 
                echo "<ol>\n"; 
                foreach ($errorMsg as $err) { 
                    echo "<li>" . $err . "</li>\n"; 
                } 
                echo "</ol>\n"; 
            } 

            print '</div>'; 
            ?> 
            

            <!--   Take out enctype line    --> 
            <form action="<? print $_SERVER['PHP_SELF']; ?>" 
                  enctype="multipart/form-data" 
                  method="post" 
                  id="frmRegister"> 
                  


				<fieldset class="contact"> 
                   

                    <label class="required" for="txtfirstName">First Name </label> 

                    <input id ="firstname" name="firstname" class="element text medium<?php if ($firstnameERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $firstname; ?>" placeholder="Your First Name" onfocus="this.select()"  tabindex="10"/> 

                </fieldset>  
                
                <fieldset class="contact"> 
         

                    <label class="required" for="txtlastName">Last Name </label> 

                    <input id ="lastname" name="lastname" class="element text medium<?php if ($lastnameERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $lastname; ?>" placeholder="Your Last Name" onfocus="this.select()"  tabindex="20"/> 

                </fieldset>  
                
                <fieldset class="contact"> 
               

                    <label class="required" for="txtEmail">Email </label> 

                    <input id ="txtEmail" name="txtEmail" class="element text medium<?php if ($emailERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $email; ?>" placeholder="Your email" onfocus="this.select()"  tabindex="30"/> 

                </fieldset>  
                
                <fieldset class="contact"> 
               

                    <label class="required" for="txtPhone">Phone Number </label> 

                    <input id ="txtPhone" name="txtPhone" class="element text medium<?php if ($phoneERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $phone; ?>" placeholder="Your Phone Number" onfocus="this.select()"  tabindex="40"/> 

                </fieldset>  



    

<fieldset class="buttons"> 
<label for="file">Filename:</label>
<input type="file" name="imgFile" id="file"><br>
</fieldset>     


<label for="comment">Enter any general comments or text for photos here</br></label>


<textarea rows="10" cols="60" name="comment">
</textarea>

<fieldset class="buttons"> 
	<input type="submit" id="btnSubmit" name="btnSubmit" value="Register" tabindex="160" class="button"> 
    <input type="reset" id="butReset" name="butReset" value="Reset Form" tabindex="170" class="button" onclick="reSetForm()" > 
</fieldset>         
                
                       

</form> 
	<?php 
    	} // end body submit 
        if ($debug) 
        print "<p>END OF PROCESSING</p>"; 
    ?> 
</section> 


	<? 
    include ("footer.php"); 
    ?> 

</body> 
</html>