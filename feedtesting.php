<?php
// Facebook class
require('facebook-library.php');
$access_token = "EAAGHU7aBAlsBAPZBZCn3ZBubF59A3L7KWuVthLzf2igbULqKtMZCUbX8ZAl18F2fRYSvEeAU2sIFWzZBIMD7pt4jFhHsKOsmuR0CIsy72V9dakQXKP32O7gKkEBCDIEcF4FpNGNZATMtRZC2tU5siPrvhlDK47ThBXPQq6FbFmM4bwZDZD";
$facebook = new Facebook($access_token);
$verify_token = "just_do_it";
$hub_verify_token = null;
if(isset($_REQUEST['hub_challenge'])) {
    $challenge = $_REQUEST['hub_challenge'];
    $hub_verify_token = $_REQUEST['hub_verify_token'];
}
if ($hub_verify_token === $verify_token) {
    echo $challenge;
}
$input = json_decode(file_get_contents('php://input'), true);
error_log(print_r($input,true));
?>
