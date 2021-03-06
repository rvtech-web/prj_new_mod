<?php if(isset($_POST['name']) == 0){ exit('No direct script access allowed'); }

require_once 'connection.php';
require_once 'mailerClass.php';

$mailerObj = new mailerClass();

 $name = $_POST['name'];
 $email = $_POST['email'];
 $pword = $_POST['pword'];
 $cpword = $_POST['cpword'];


if(empty($name) || empty($email) || empty($pword) || empty($cpword)){
 header('Location: signup.php?msg=One of the required field is empty');
 exit;
}

 if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    header('Location:signup.php?msg=Email address is not valid');
    exit;
}
 if($cpword != $pword){
     echo 'not same';
	 $_POST['kol'] = 'Password does not match';
 
	header('Location: signup.php?msg=Password does not match');
	exit;
 }

$query = $GLOBALS['connect']->prepare("SELECT * FROM members where email = :email");

$query->execute(array(':email'=> $email));

 if($query->rowCount() > 0){
	header('Location: signup.php?msg=This email address is already registered, please use different one.');
	exit;

 }else{

 $shaPword = sha1($pword);
     $finalquery = $GLOBALS['connect']->prepare("INSERT INTO members(name,email,password) VALUES(:name,:email,:password)");
     $finalquery->execute(array(
        ':name' => $name,
        ':email' => $email,
        ':password' => $shaPword
     ));

     $senderName = 'Application Admin';
     $senderEmail = 'mehar.kayako@gmail.com';
     $subject = 'Registration Email';
     $body = sprintf("Hi %s,\n\nThank you for registering in our application. Your login details are:\n\nEmail : %s \n Password %s\n\nRegards,\n\nApplication Admin",$name,$email,$pword);
     $altBody = 'This is the body in plain text for non-HTML mail clients';
     $result = $mailerObj->sendMail($email,$name,$subject,$body,$senderEmail,$senderName);

 }

if($finalquery->rowCount()){
	header('Location: signup.php?msg=Registered sucesfully');
	exit;
 }else{
	header('Location: signup.php?msg=Sorry! Try again');
   exit;
 }
?>