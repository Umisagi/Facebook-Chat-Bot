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
//error_log("****INPUT : ".print_r($input,true));
$page_id = $input['entry'][0]['id'];
$thread_id = "Null";
$message_id = "m_".$input['entry'][0]['messaging'][0]['message']['mid'];
$message = $input['entry'][0]['messaging'][0]['message']['text']; // Incoming message
$attachment = $input['entry'][0]['messaging'][0]['message']['attachments'];
$results = $facebook->api("/{$message_id}")->fields('from, to, created_time')->get();
if($results->error):
    error_log('*************Error : '.print_r($results,true));
endif;
$sender_id = $results->from->id;
$sebder_name = $results->from->name;
$sender_email = $results->from->email;
$receiver_id = $results->to->data[0]->id;
$receiver_name = $results->to->data[0]->name;
$receiver_email = $results->to->data[0]->email;
$created_time = $results->created_time; // Epoch timestamp
$time = $input['entry'][0]['messaging'][0]['timestamp']*0.001; // Ignore millisecond
$time = floor($time);
$time = $time-5;

// Search mid for tid
// Incoming message
/**if(empty($input['entry'][0]['messaging'][0]['message']['is_echo'])):
    $mid = "m_".$input['entry'][0]['messaging'][0]['message']['mid']; // Message ID
    $results = $facebook->api("/{$mid}")->fields('from, to, created_time')->get();
    if($results->error):
        error_log('*************Error : '.print_r($results,true));
        error_log("##########Error mid : ".$mid);
    endif;
    $userid = $results->from->id;
    $username = $results->from->name;
    $pageid = $results->to->data[0]->id;
    $pagename = $results->to->data[0]->name;
    $updatedtime = $results->created_time; // Updated time of thread & create time of message
    //Chk db

    // If threadID not found
    if (!isset($threadid)):
        $results = $facebook->api("/me/threads")->fields('participants, updated_time')->since($time)->get();
        //error_log("-----Threads : ".print_r($results,true));
        while (isset($results->paging)): // If have more than 25 threads
            $nextthread = $results->paging->next;
            foreach($results->data as $thread):
                if($thread->participants->data[0]->id == $userid && $thread->participants->data[1]->id == $pageid):
                    $threadid = $thread->id;
                    $createdtime = $thread->updated_time; // Create time of thread
                endif;
            endforeach;
            $results = $facebook->getnext($nextpage); // Go to nextpage
        endwhile;
    endif;
    error_log("####User Sending Mode####");

// Outgoing message
elseif(!empty($input['entry'][0]['messaging'][0]['message']['is_echo'])): 
    $mid = "m_".$input['entry'][0]['messaging'][0]['message']['mid']; // Message ID
    $results = $facebook->api("/{$mid}")->fields('from, to, created_time, message')->get();
    if($results->error):
        error_log('*************Error : '.print_r($results,true));
        error_log("##########Error mid : ".$mid);
    endif;
    $pageid = $results->from->id; 
    $pagename = $results->from->name;
    $userid = $results->to->data[0]->id;
    $username = $results->to->data[0]->name;
    $createdtime = $results->created_time; // Updated time of thread & create time of message
    $message = $results->message;
    // Chk db

    // If threadID not found
    if (!isset($threadid)):
        $results = $facebook->api("/me/threads")->fields('participants, updated_time')->since($time)->get();
        //error_log("/*-/*-Results : ".print_r($results,true));
        while (isset($results->paging)): // If have more than 25 threads
            $nextthread = $results->paging->next;
            foreach($results->data as $thread):
                if($thread->participants->data[0]->id == $userid && $thread->participants->data[1]->id == $pageid):
                    $threadid = $thread->id;
                    $updatetime = $thread->updated_time; // Create time of thread
                endif;
            endforeach;
            $results = $facebook->getnext($nextpage); // Go to nextpage
        endwhile;
    endif;
    error_log("####Page Sending Mode####");
endif;**/

//error_log("*****************************");
//error_log("------Thread ID : ".$threadid);
//error_log("------Create Time : ".$ctime);
//error_log("------Update Time : ".$updatetime);
error_log("*****************************");
error_log("------Page_id : ".$page_id);
error_log("------Thread_id : ".$thread_id);
error_log("------Message_id : ".$message_id);
error_log("------Message : ".$message);
error_log("------Attachment : ".print_r($attachment,true));
error_log("------Sender_id: ".$sender_id);
error_log("------Sebder_name : ".$sebder_name);
error_log("------Sender_email : ".$sender_email);
error_log("------Receiver_id: ".$receiver_id);
error_log("------Receiver_name : ".$receiver_name);
error_log("------Receiver_email : ".$receiver_email);
error_log("------Created_time : ".$created_time);
