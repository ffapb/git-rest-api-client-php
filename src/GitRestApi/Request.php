<?php

namespace GitRestApi;

class Request {

  function __construct(string $method, string $endpoint, array $params=[], string $action=null, string $reponame=null, string $path=null, string $attachment=null) {
    if(!parse_url($endpoint)) {
      throw new \Exception("Invalid endpoint URL: $endpoint");
    }
    // strip trailing slash
    // http://stackoverflow.com/a/3710970/4126114
    $endpoint = rtrim($endpoint,'/');

    $this->method   = $method;
    $this->endpoint = $endpoint;
    $this->params   = $params;
    $this->action   = $action;
    $this->reponame = $reponame;
    $this->path     = $path;
    $this->attachment = $attachment;
  }

  public function url() {
    $constituents = [
      $this->endpoint,
      is_null($this->reponame)?$this->reponame:'repo/'.$this->reponame,
      $this->action,
      $this->path
    ];
    $constituents = array_filter(
      $constituents,
      function($x) { return !is_null($x); }
    );
    $url = implode('/',$constituents);

    return $url;
  }

  // method: string from https://github.com/nategood/httpful/blob/master/src/Httpful/Http.php#L11
  public function send() {
    $request = \Httpful\Request::init()
      ->method($this->method)
      ->uri($this->url());

    if(!is_null($this->attachment)) {
      $request = $request->attach(array('file' => $this->attachment));
    }

    if(count($this->params)>0) {
        $request = $request
          ->sendsJson()
          ->body(json_encode($this->params));
    }
    $response = $request->send();

    if(isset($response->body->error)) {
      throw new \Exception($response->body->error);
    }

//    if($method=='GET' && $this->action=='ls-tree') var_dump($response);
    return $response->body;
  }

}

