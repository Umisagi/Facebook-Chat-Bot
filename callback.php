<?php
/**
 * Include important files
 */
//----------------require('CDatabase.php');
require('CMessageFacebook.php');
require('facebook-library.php');
/**
 *Code
 */
//----------------$database = new CDatabase;
//----------------$database->Connect();
$verify_token = "just_do_it";
$hub_verify_token = null;
if(isset($_REQUEST['hub_challenge'])) 
{
    $challenge = $_REQUEST['hub_challenge'];
    $hub_verify_token = $_REQUEST['hub_verify_token'];
}
if ($hub_verify_token === $verify_token) 
{
    echo $challenge;
}
$input = json_decode(file_get_contents('php://input'), true);
error_log("input : ".$input);
//---------------$sqlString = "SELECT * FROM WEBHOOK_MESSAGE_THREAD WHERE (USER_ID = '".$input['entry'][0]['messaging'][0]['recipient']['id']."' OR USER_ID = '".$input['entry'][0]['messaging'][0]['sender']['id']."') AND PAGE_ID ='".$input['entry'][0]['id']."'";
//---------------$query = $database->ExecuteReader2($sqlString, array());
//$message_smm = new CMessageFacebook;
$access_token = "EAAGHU7aBAlsBAKo1nqpDXS9DPIFgYaj6L05uEm2arLZBsFEvNpgYqg3dlxmYCbppRrNUl6QJNGu8GwghZC9LbWRsgXoZAyuaRwKuSV8ZAo5WtG1bsIvfbzTNEoX397AZAma3xDjBFv8ZCGAwCdUmB7fnStWepmJ6a5hTl4ntJLzwZDZD";
$thread_id = '';
//---------------if(count($query)>0)
/*if(0)
{
	//$objects = $message_smm->getDataMessage($input, $access_token, $thread_id);
} else
{
	//$objects = $message_smm->getThreadID($input, $access_token);
}*/
//error_log("-----------objects : ".$objects);
