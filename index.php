<?php

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
//error_log("****IndexINPUT : ".print_r($input,true));
$sender = $input['entry'][0]['messaging'][0]['sender']['id']; // ID to send back 
$message = $input['entry'][0]['messaging'][0]['message']['text'];

$messageID = $input['entry'][0]['messaging'][0]['message']['mid'];
//error_log("MID : ".$messageID);
//API Url
$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;
$stateurl = 'https://calm-retreat-75905.herokuapp.com/callback.php';
//$stateurl = 'http://cl1dev.smm.ais.co.th/SMMGetInfo/eric_meter/callbackstatus.php';

//State creating
if (preg_match("/โอนสาย/i", $message)) {
    $status = "transfer";
} elseif (preg_match("/สวัสดี/i", $message)) {
    $status = "start";
} else {
    $status = "chatting";
}
// MID & TID 
$results = $facebook->api("/m_".$messageID)->fields('from, to, created_time')->get();
$sender_id = $results->from->id;
$receiver_id = $results->to->data[0]->id;
$created_time = $results->created_time;
//error_log("Sender ID : ".$sender_id);
//error_log("Receiver ID : ".$receiver_id);
$results = $facebook->api("/me/threads")->fields('participants')->get();
    while (isset($results->paging)): // If have more than 25 threads
        $nextthread = $results->paging->next;
        foreach($results->data as $thread):
            if(($thread->participants->data[0]->id == $sender_id || $thread->participants->data[0]->id == $receiver_id)):
                $thread_id = $thread->id;
            endif;
        endforeach;
        $results = $facebook->getnext($nextpage); // Go to nextpage
    endwhile;
//error_log("Tid : ".$thread_id);
//Initiate cURL.
$ch = curl_init($url);
$statech = curl_init($stateurl);
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
$jsonstatedata = '{"status" :"'.$status.'","thread_id":"'.$thread_id.'","time":"'.$created_time.'","msg_id":"m_'.$messageID.'"}';
//Encode the array into JSON.
$jsonDataEncoded = $jsonData;
$jsonstateEncoded = $jsonstatedata;
//error_log("####OUTPUT : ".print_r($jsonDataEncoded,true));
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($statech, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
curl_setopt($statech, CURLOPT_POSTFIELDS, $jsonstateEncoded);
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($statech, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

//curl_setopt($statech, CURLOPT_RETURNTRANSFER, true);
//error_log("stateb4encoded : ".print_r($jsonstatedata,true));
//error_log("statedata : ".print_r($jsonstateEncoded,true));
//Execute the request
if(!empty($input['entry'][0]['messaging'][0]['message'])){   
    $result = curl_exec($ch);
    $stateresult = curl_exec($statech);
}
//error_log("state : ".$stateresult);


