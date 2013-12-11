<?php 
//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ 
// 
// This function mails the text passed in to the people specified  
// it requires the person sending it to and a message  
// CONSTRAINTS: 
//      $to must not be empty 
//      $to must be an email format 
//      $subject must not be empty 
//      $message must not be empty 
//      $message must have a minium number of characters 
//      $message should be cleand of invalid html before being sent here 
// 
// Written by Connor Williams and Robert Erickson cwilli20@uvm.edu robert.erickson@uvm.edu
// 
// function returns a boolean value 
function sendMail($to, $subject, $message){  
    $MIN_MESSAGE_LENGTH=40; 
     
    // just checking to make sure the values passed in are reasonable 
    if(empty($to)) return false; 
    if(!(preg_match("/^([[:alnum:]]|_|\.|-)+@([[:alnum:]]|\.|-)+(\.)([a-z]{2,4})$/",$to))) return false; 
     
    if(empty($subject)) return false; 
     
    if(empty($message)) return false; 
    if (strlen($message)<$MIN_MESSAGE_LENGTH) return false; 
     
    $to = htmlentities($to,ENT_QUOTES,"UTF-8"); 
    $subject = htmlentities($to,ENT_QUOTES,"UTF-8"); 
     
    // we cannot push message into html entites or we lose the format 
    // of our email so be sure to do that before sending it to this function 
     
    // be sure to change Your Site and yoursite to something meaningful 
    $mailFrom = "Crazy Adventures Confirmation"; 

    //$cc = "";
    //$bcc = ""; 
    $bcc = "cwilli20@uvm.edu"; 

    /* message */ 
    $messageTop  = '<html><head><title>' . $subject . '</title></head><body>'; 
    $mailMessage = $messageTop . $message; 

    $headers  = "MIME-Version: 1.0\r\n"; 
    $headers .= "Content-type: text/html; charset=utf-8\r\n"; 

    $headers .= "From: " . $mailFrom . "\r\n"; 

    if ($cc!="") $headers .= "CC: " . $cc . "\r\n"; 
    if ($bcc!="") $headers .= "Bcc: " . $bcc . "\r\n"; 

    /* this line actually sends the email */ 
    $blnMail=mail($to, $subject, $mailMessage, $headers); 
     
    return $blnMail; 
} 
?>