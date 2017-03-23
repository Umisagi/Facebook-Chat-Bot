<?php
/**
 * Webhook for Time Bot- Facebook Messenger Bot
 * User: adnan
 * Date: 24/04/16
 * Time: 3:26 PM
 */
// Facebook class
class Facebook {

  /**
   * Default variables
   */
  protected $url = 'https://graph.facebook.com/v2.8';
  protected $filter = 'toplevel'; /* toplevel or stream */
  protected $limit = 10;
  private $timeout = 300;

  public function __construct($access_token = ''){
    $this->access_token = $access_token;
  }

  /**
   * Calling functions
   */
  private function generateRequestURL(){
    $queries = [];
    foreach($this as $key => $value):
      $ignore = ['timeout', 'api', 'url'];
      if(!in_array($key, $ignore)):
        $queries[$key] = $value;
      endif;
    endforeach;
    $this->url_request = $this->url.$this->api.'?'.http_build_query($queries);
    return $this;
  }

  public function api($api){
    $this->api = $api;
    return $this;
  }

  public function get(){
    $this->generateRequestURL();
    return json_decode($this->doRequest());
  }

  public function getnext($nextpage){
    $this->url_request = $nextpage;
    return json_decode($this->doRequest());
  }

  public function getJSON(){
    $this->generateRequestURL();
    return $this->doRequest();
  }

  /**
   * Request to Facebook API
   */
  private function doRequest(){
    $curl = curl_init();
            curl_setopt($curl, CURLOPT_PROXY, "proxyb.ais.co.th:2520");
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_URL, $this->url_request);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36");
    $results = curl_exec($curl);
    $get_info = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return $results;
  }

  /**
   * Methods
   */
  public function accessToken($accessToken){
    $this->access_token = $accessToken;
    return $this;
  }
  public function fields($fields){
    $this->fields = $fields;
    return $this;
  }
  public function since($time){
    $this->since = $time;
    return $this;
  }
  public function until($time){
    $this->until = $time;
    return $this;
  }
  public function filter($filter){
    $this->filter = $filter;
    return $this;
  }
  public function order($order){
    $this->order = $order;
    return $this;
  }
  public function offset($offset){
    $this->offset = $offset;
    return $this;
  }
  public function limit($limit){
    $this->limit = $limit;
    return $this;
  }

}

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
$result = facebook->api($mid)->fields('from,to')->get();
if($results->error):
  return return_error('Notifications', $results->error);
endif;
error_log("####ApiResults : ".$mid);

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

