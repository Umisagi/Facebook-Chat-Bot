<?php
/**
 * Webhook for Time Bot- Facebook Messenger Bot
 * User: adnan
 * Date: 24/04/16
 * Time: 3:26 PM
 */
// Facebook class
require('facebook-library.php');

$access_token = "EAAGHU7aBAlsBAKo1nqpDXS9DPIFgYaj6L05uEm2arLZBsFEvNpgYqg3dlxmYCbppRrNUl6QJNGu8GwghZC9LbWRsgXoZAyuaRwKuSV8ZAo5WtG1bsIvfbzTNEoX397AZAma3xDjBFv8ZCGAwCdUmB7fnStWepmJ6a5hTl4ntJLzwZDZD";
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
//logWrite("Input : ".print_r($input,true));
//error_log("####INPUT : ".print_r($input,true));
$sender = $input['entry'][0]['messaging'][0]['sender']['id'];
$mid = $input['entry'][0]['messaging'][0]['message']['mid'];
$message = $input['entry'][0]['messaging'][0]['message']['text'];
error_log("####Messageid : ".$mid);
$message_to_reply = '';

// Search mid for tid
$results = $facebook->api("/me")->get();
if($results->error):
  return return_error('Notifications', $results->error);
endif;
$facebook->getrekt();

/**
 * Some Basic rules to validate incoming messages
 */

//API Url
$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;


//Initiate cURL.
$ch = curl_init($url);
//The JSON data.
    $jsonData = '{
    "recipient":{
        "id":"'.$sender.'"
    },
    "message":{
    "attachment":{
      "type":"template",
      "payload":{
        "template_type":"button",
        "text":"What do you want to do next?",
        "buttons":[
          {
            "type":"web_url",
            "url":"https://petersapparel.parseapp.com",
            "title":"Show Website"
          },
          {
            "type":"postback",
            "title":"Start Chatting",
            "payload":"USER_DEFINED_PAYLOAD"
          },
          {
            "type":"postback",
            "title":"Start Chatting",
            "payload":"USER_DEFINED_PAYLOAD"
          }
        ]
      }
    }
  }
}';

//Encode the array into JSON.
$jsonDataEncoded = $jsonData;
error_log("####OUTPUT : ".print_r($jsonDataEncoded,true));
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);

//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));



//Execute the request
if(!empty($input['entry'][0]['messaging'][0]['message'])){
        
    $result = curl_exec($ch);
}

