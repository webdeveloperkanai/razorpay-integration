<?php

require('config.php');
// session_start();
//db connection

require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

    $error = "Payment Failed";

    date_default_timezone_set('Asia/Kolkata');
    $date = date("d-M-Y");
    $ddate = date("dmYhis");
    $time = date("h:i A");
    $txn = $_POST['razorpay_payment_id']; 


if (empty($_POST['razorpay_payment_id']) === false)
{
    $api = new Api($keyId, $keySecret);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}

if ($success === true)
{

$email = urldecode(base64_decode($_GET['e'])); 
$q = mysqli_query($con,"SELECT * FROM  `orders` WHERE `email`='$email' and `status`='Pending' order by id desc ");
$order = mysqli_fetch_array($q);

$oid = $order['id'];

$hid = $order['hotel_id']; 
    
    
     
    $name = $order['name']; 
    $email = $order['email']; 
    $phone = $order['phone']; 
    $address = 'Demo Address'; 
    
    $uid = $order['uid'];
    
    $amount = $order['amount']; 
 
 
    $price = $order['amount']; 
      

    $razorpay_order_id = $_SESSION['razorpay_order_id'];
    $razorpay_payment_id = $_POST['razorpay_payment_id'];
     
    // $price = $_SESSION['price'];
      

    if(mysqli_query($con, "UPDATE `orders` SET `transactionId`='$txn',`status`='Success',`date`='$date',`time`='$time' WHERE `id`='$oid' ")){
            $em = $_GET['e'];
            
        $trn = mysqli_query($con,"INSERT INTO `transactions` SET `txn`='$txn',`status`='Live',`type`='Credit',`date`='$date',`time`='$time',`title`='Room Booking',`amount`='$amount',`uid`='$uid',`hid`='$hid' "); 
        
        echo "<script>location.href='../thankyou?e=$em'</script>";
    }

   // echo "<script>"

    
}
else
{

    echo "<script>alert('Payment failed , please check your payment details. ')</script>";
    // echo "<script>location.href='../dashboard.php'</script>";
}

// echo $html;
