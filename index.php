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
error_log("****INPUT : ".print_r($input,true));
$sender = $input['entry'][0]['messaging'][0]['sender']['id']; // ID to send back 

$message = $input['entry'][0]['messaging'][0]['message']['text']; // Message
$time = $input['entry'][0]['messaging'][0]['timestamp']*0.001;
$time = floor($time);
$time = $time-1;

$message_to_reply = '';

// Search mid for tid

if(!empty($input['entry'][0]['messaging'][0]['message'])):
    $mid = "m_".$input['entry'][0]['messaging'][0]['message']['mid']; // Message ID
    $results = $facebook->api("/{$mid}")->fields('from, to, created_time')->get();
    if($results->error):
      return error_log('*************Error : '.print_r($results,true));
    endif;
    $userid = $results->from->id;
    $username = $results->from->name;
    $pageid = $results->to->data[0]->id;
    $pagename = $results->to->data[0]->name;
    $createdtime = $results->created_time;

    //error_log("----Time : ".$time);
    //Chk db

    // If not found
    if (!isset($threadid)):
        $results = $facebook->api("/me/threads")->fields('participants')->since($time)->get();
        //error_log("-----Threads : ".print_r($results,true));
        while (isset($results->paging)):
            $nextthread = $results->paging->next;
            foreach($results->data as $thread):
                // Chking if not null
                if($thread->participants->data[0]->id == $userid && $thread->participants->data[1]->id == $pageid):
                    $threadid = $thread->id;
                    $updatetime = $thread->updated_time;
                endif;
                //error_log("-----Threadid : ".$threadid);
            endforeach;
            $results = $facebook->getnext($nextpage);
        endwhile;
    endif;
    error_log("####Sending mode####");

elseif(!empty($input['entry'][0]['messaging'][0]['delivery'])):
    $mid = "m_".$input['entry'][0]['messaging'][0]['delivery']['mid'][0];
    $results = $facebook->api("/{$mid}")->fields('from, to, created_time')->get();
    if($results->error):
      return error_log('*************Error : '.print_r($results,true));
    endif;
    $pageid = $results->from->id;
    $pagename = $results->from->name;
    $userid = $results->to->data[0]->id;
    $username = $results->to->data[0]->name;
    $createdtime = $results->created_time;
    $message = $results->message;
    // Chk db

    // If not found
    if (!isset($threadid)):
        $results = $facebook->api("/me/threads")->fields('participants')->since($time)->get();
        while (isset($results->paging)):
            $nextthread = $results->paging->next;
            foreach($results->data as $thread):
                // Chking if not null
                if($thread->participants->data[0]->id == $userid && $thread->participants->data[1]->id == $pageid):
                    $threadid = $thread->id;
                    $updatetime = $thread->updated_time;
                endif;
            endforeach;
            $results = $facebook->getnext($nextpage);
        endwhile;
    endif;
    error_log("####Reciving mode####");
endif;
error_log("------Userid : ".$userid);
error_log("------Username : ".$username);
error_log("------Pageid : ".$pageid);
error_log("------Pagename : ".$pagename);
error_log("------Thread ID : ".$threadid);
error_log("------Message ID : ".$mid);
error_log("------Message : ".$message);
error_log("------Time : ".$createdtime);
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
//error_log("####OUTPUT : ".print_r($jsonDataEncoded,true));
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

