<?php

/**
 * Facebook PHP Simple Library
 * Created by Krissada Boontrigratn
 * Website http://tzv.me
 * Email im@tzv.me
 */

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
  
  public function getrekt(){
    error_log("####Messageid : Rekttesting");
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
            //curl_setopt($curl, CURLOPT_PROXY, "proxyb.ais.co.th:2520");
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
    error_log($curl);
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
