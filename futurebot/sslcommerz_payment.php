<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email']) || !isset($_POST['course'], $_POST['price'])) {
    header("Location: course_suggestions.php");
    exit;
}

$course = $_POST['course'];
$amount = $_POST['price'];
$user_email = $_SESSION['user_email'];

// SSLCommerz Sandbox Credentials
$store_id = "your_sandbox_store_id"; // replace with your sandbox Store ID
$store_passwd = "your_sandbox_store_password"; // replace with your sandbox password

$tran_id = "FTB".uniqid(); // unique transaction id

$post_data = array(
    'store_id' => $store_id,
    'store_passwd' => $store_passwd,
    'total_amount' => $amount,
    'currency' => "BDT",
    'tran_id' => $tran_id,
    'success_url' => "http://yourdomain.com/ssl_success.php",
    'fail_url' => "http://yourdomain.com/ssl_fail.php",
    'cancel_url' => "http://yourdomain.com/ssl_fail.php",
    'emi_option' => 0,
    'cus_name' => $user_email,
    'cus_email' => $user_email,
    'cus_add1' => "",
    'cus_add2' => "",
    'cus_city' => "",
    'cus_state' => "",
    'cus_postcode' => "",
    'cus_country' => "Bangladesh",
    'cus_phone' => "01738915382", // ✅ placeholder phone
    'shipping_method' => "NO",
    'product_name' => $course,
    'product_category' => "Digital Course",
    'product_profile' => "digital"
);

$direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";

$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $direct_api_url );
curl_setopt($handle, CURLOPT_POST, 1 );
curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data );
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

$content = curl_exec($handle);
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

if($code == 200 && !( curl_errno($handle))) {
    $sslcommerzResponse = json_decode($content, true);
    if(isset($sslcommerzResponse['GatewayPageURL']) && $sslcommerzResponse['GatewayPageURL'] != "") {
        header("Location: ".$sslcommerzResponse['GatewayPageURL']);
        exit;
    } else {
        echo "<h3>Payment initiation failed!</h3>";
        echo "<pre>";
        print_r($sslcommerzResponse);
        echo "</pre>";
    }
} else {
    echo "CURL Error: ".curl_error($handle);
}
curl_close($handle);
?>
