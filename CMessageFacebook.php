<?php
class CMessageFacebook {

	public function __construct($access_token = ''){
		$this->access_token = $access_token;
		$this->facebook = new Facebook($this->access_token);
	}

	public function getDataMessage( $input, $threadid){
		$callback = [];
		$results = $this->$facebook->api("/m_".$input['entry'][0]['messaging'][0]['message']['mid'])->fields('from, to, created_time')->get();
		if($results->error):
		    error_log('*************Error : '.print_r($results,true));
		endif;
		//data
		$thread_id = $threadid;
    		$page_id = $input['entry'][0]['id'];
		$message_id = "m_".$input['entry'][0]['messaging'][0]['message']['mid'];
		$message = $input['entry'][0]['messaging'][0]['message']['text']; // Incoming message
		$attachment = $input['entry'][0]['messaging'][0]['message']['attachments'];
		$sender_id = $results->from->id;
		$sebder_name = $results->from->name;
		$sender_email = $results->from->email;
		$receiver_id = $results->to->data[0]->id;
		$receiver_name = $results->to->data[0]->name;
		$receiver_email = $results->to->data[0]->email;
		$created_time = $results->created_time;
		$data = (object)array(
			"thread_id" => $thread_id,
			"page_id" => $page_id,
			"message_id" => $message_id,
			"message" => $message,
			"attachment" => $attachment,
			"sender_id" => $sender_id,
			"sebder_name" => $sebder_name,
			"sender_email" => $sender_email,
			"receiver_id" => $receiver_id,
			"receiver_name" => $receiver_name,
			"receiver_email" => $receiver_email,
			"created_time" => $created_time,
		);
		  $callback[] = $data;
		  return $callback;
		}

  	public function getThreadID($input){
  		$callback = [];
  		$results = $this->$facebook->api("/m_".$input['entry'][0]['messaging'][0]['message']['mid'])->fields('from, to, created_time')->get();
		if($results->error):
		    error_log('*************Error : '.print_r($results,true));
		endif;
		//data
		$page_id = $input['entry'][0]['id'];
		$message_id = "m_".$input['entry'][0]['messaging'][0]['message']['mid'];
		$message = $input['entry'][0]['messaging'][0]['message']['text']; // Incoming message
		$attachment = $input['entry'][0]['messaging'][0]['message']['attachments'];
		$sender_id = $results->from->id;
		$sebder_name = $results->from->name;
		$sender_email = $results->from->email;
		$receiver_id = $results->to->data[0]->id;
		$receiver_name = $results->to->data[0]->name;
		$receiver_email = $results->to->data[0]->email;
		$created_time = $results->created_time;
		$time = $input['entry'][0]['messaging'][0]['timestamp']*0.001; // Ignore millisecond
		$time = floor($time);
		$time = $time-5;
		$results = $this->$facebook->api("/me/threads")->fields('participants')->since($time)->get();
		while (isset($results->paging)): // If have more than 25 threads
		    $nextthread = $results->paging->next;
		    foreach($results->data as $thread):
		        if(($thread->participants->data[0]->id == $sender_id || $thread->participants->data[0]->id == $receiver_id) && $thread->participants->data[1]->id == $page_id):
		            $thread_id = $thread->id;
		        endif;
		    endforeach;
		    $results = $this->$facebook->getnext($nextpage); // Go to nextpage
		endwhile;
		$data = (object)array(
			"thread_id" => $thread_id,
            "page_id" => $page_id,
            "message_id" => $message_id,
            "message" => $message,
            "attachment" => $attachment,
            "sender_id" => $sender_id,
            "sebder_name" => $sebder_name,
            "sender_email" => $sender_email,
            "receiver_id" => $receiver_id,
            "receiver_name" => $receiver_name,
            "receiver_email" => $receiver_email,
            "created_time" => $created_time,
          );
          $callback[] = $data;
          return $callback;
  	}
}
