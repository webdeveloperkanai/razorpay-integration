<?php
    session_start(); 

   // $red = $_GET['pid'];
 

//These should be commented out in production
// This is for error reporting
// Add it to config.php to report any errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

$con = mysqli_connect('localhost','root','','ngo'); 


if(!isset($_SESSION['auth'])) {
    header("location: /login"); 
} 

$email = $_SESSION['auth']; 

$us = mysqli_query($con,"SELECT * FROM `patients` WHERE `email`='$email' and `status`!='Deleted' "); 
$user = mysqli_fetch_array($us); 


$keyId = "rzp_test_RwLXfXA2MokrCH";
$keySecret = "Pt9ywfna03rzsIZ92U9oSfSh";
$displayCurrency = 'INR';
 
// if($user['address']==null) {
    
//     echo "<script>alert('You must add address')</script>";
//     echo "<script>location.href='../profile.php'</script>";
// }