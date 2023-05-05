<?php


require('razorpay-php/Razorpay.php'); 
// session_start();

// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders

$email = urldecode(base64_decode($_GET['e'])); 

$q = mysqli_query($con,"SELECT * FROM `orders` WHERE `email`='$email' order by id desc "); 
$res = mysqli_fetch_array($q);

$amount = $res['amount'];

$price = $res['amount']; 


$hotel_id = $res['hotel_id']; 

$h = mysqli_query($con,"SELECT * FROM  `hotels` WHERE `id`='$hotel_id' "); 
$hotel = mysqli_fetch_array($h); 




$pid = $res['id']; 

$_SESSION['pid'] = $pid; 
 

$_SESSION['price'] = $price; 

$customername = $user['name'];
$email = $user['email'];
$_SESSION['email'] = $email;
$contactno = $user['phone'];

$orderData = [
    'receipt'         => 3456,
    'amount'          => $price * 100, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$displayAmount = $amount = $orderData['amount'];

if ($displayCurrency !== 'INR')
{
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
    $exchange = json_decode(file_get_contents($url), true);

    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
}

$com_name = $com['name'];
$com_logo = $com['logo'];

$data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => $com_name,
    "description"       => "Coding for Everyone",
    "image"             => "../admin/img/$com_logo",
    "prefill"           => [
    "name"              => $customername,
    "email"             => $email,
    "contact"           => $contactno,
    ],
    "notes"             => [
    "address"           => "Hello World",
    "merchant_order_id" => "12312321",
    ],
    "theme"             => [
    "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];

if ($displayCurrency !== 'INR')
{
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">

<title>Checkout - <?php echo $hotel['name'] ?> </title>

<form action="verify.php?e=<?php  echo $_GET['e'] ?>" method="POST" >
    <center>
        <h3>Check details</h3>  
    </center>
    <table class="table">
      <tr><td>Hotel</td> <td>: <?php echo $hotel['name'] ?></td> </tr>
      <tr><td>Price</td> <td>: ₹ <?php  echo $amount/100 ?>   </td> </tr>
      <tr><td>Rooms</td> <td>: <?php echo $res['rooms'] ?></td> </tr>
      <tr><td>Check In</td> <td>: <?php echo $res['check_in'] ?></td> </tr>
      <tr><td>Check Out</td> <td>: <?php echo $res['check_out'] ?></td> </tr>
      <tr><td>Adults</td> <td>: <?php echo $res['adults'] ?></td> </tr>
      <tr><td>Children</td> <td>: <?php echo $res['children'] ?></td> </tr>
       
      <tr><td>Name</td> <td>: <?php echo $res['name'] ?></td> </tr>
      <tr><td>Email</td> <td>: <?php echo $res['email'] ?></td> </tr>
      
    </table>

 

  <script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="<?php echo $data['key']?>"
    data-amount="<?php  echo round($price) ; ?>"
    data-currency="INR"
    data-name="<?php echo $data['name']?>"
    data-image="<?php echo $data['image']?>"
    data-description="<?php echo $data['description']?>"
    data-prefill.name="<?php echo $data['prefill']['name']?>"
    data-prefill.email="<?php echo $data['prefill']['email']?>"
    data-prefill.contact="<?php echo $data['prefill']['contact']?>"
    data-notes.shopping_order_id="3456"
    data-order_id="<?php echo $data['order_id']?>"
    <?php if ($displayCurrency !== 'INR') { ?> data-display_amount="<?php echo $data['display_amount']?>" <?php } ?>
    <?php if ($displayCurrency !== 'INR') { ?> data-display_currency="<?php echo $data['display_currency']?>" <?php } ?>
  >
  </script>
  <!-- Any extra fields to be submitted with the form but not sent to Razorpay -->
  <input type="hidden" name="shopping_order_id" value="3456">

  <a href="../">Cancel</a>
</form>

<style>
body {
  display:flex;
  align-items:center;
  justify-content:center;
}
  .razorpay-payment-button {
    height: 50px; width:100%;
    background: black; 
    color:white;
    border:0px;
    transition:0.5s;
  }
  .razorpay-payment-button:hover {
    background:transparent;
    border:1px solid #000; 
    color:#000; 
  }
  form{
    max-width:500px;
    padding:15px;
    background:<?php if($_GET['pid'] %2 !=0) { echo "lightblue";  } else { echo "orange"; }  ?>;
    border-radius:15px;
    box-shadow: 0 10px 50px 10px #878;
  }
  tr td:nth-child(1) {
    font-weight:bold;
  }
   
</style>