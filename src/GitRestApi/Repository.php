<?php

namespace GitRestApi;

class Repository {

  function __construct(Client $client,string $reponame) {
    $this->client = $client;
    $this->reponame = $reponame;
  }

  private function path(string $part2) {
    return $this->client->path(
      'repo/'.$this->reponame.'/'.$part2
    );
  }

  public function get(string $key) {
    $response = \Httpful\Request::get(
      $this->path('tree/'.$key)
    )->send();
    Client::handleError($response);
    return $response->body;
  }

  public function put(string $key, string $value) {
    // save to a temporary file
    $file_name_with_full_path = tempnam(sys_get_temp_dir(), 'FOO');
    file_put_contents($file_name_with_full_path,$value);

    // create curl file object for PUT
    $cFile = curl_file_create($file_name_with_full_path);
    $params = array('file'=> $cFile);

    // send PUT request
    $response = \Httpful\Request::put(
      $this->path('tree/'.$key),
      $params,
      \Httpful\Mime::UPLOAD
    )->send();
    Client::handleError($response);
    return $response->body;
  }

  public function deleteAll() {
    $response = \Httpful\Request::delete($this->path(''))->send();
    Client::handleError($response);
    return $response->body;
  }

  public function deleteKey(string $key) {
    $response = \Httpful\Request::delete($this->path('tree/'.$key))->send();
    Client::handleError($response);
    return $response->body;
  }

  public function configPut(string $name, string $value) {
    $response = \Httpful\Request::put($this->path('config'))
        ->sendsJson()
        ->body(json_encode(['name'=>$name,'value'=>$value]))
        ->send();
    Client::handleError($response);
    return $response->body;
  }
/*
  private function configPutIfNotExists(string $name, string $value) {
    $response = \Httpful\Request::get($this->path('config'))
        ->sendsJson()
        ->body(json_encode(['name'=>$name]))
        ->send();
    Client::handleError($response);

    if(!in_array($name,$response->body)) {
      if(is_null($userName)) {
        throw new \Exception('Need to config repo '.$name.'. Please pass it to commit(...)');
      }
      $this->updateConfig($name,$value);
    }
  }
*/

  public function commit(string $message, string $userName=null, string $userEmail=null) {
    // check if a user name and email are configured
    if(!is_null($userName)) {
      $this->updateConfigIfNotExists('user.name',$userName);
    }
    if(!is_null($userEmail)) {
      $this->updateConfigIfNotExists('user.email',$userEmail);
    }

    //
    $response = \Httpful\Request::post(
      $this->path('commit')
    )   ->sendsJson()
        ->body(json_encode(['message'=>$message]))
        ->send();
    Client::handleError($response);
    return $response->body;
  }

  // push commits
  public function push(string $remote=null) {
    $params=[];
    if(!is_null($remote)) {
      $params=array_merge(
        $params,
        ['remote'=>$remote]
      );
    }
    $response = \Httpful\Request::post(
      $this->path('push')
    )   ->sendsJson()
        ->body(json_encode($params))
        ->send();
    Client::handleError($response);
    return $response->body;
  }

  public function pull() {
    $response = \Httpful\Request::post($this->path('pull'))
        ->send();
    Client::handleError($response);
    return $response->body;
  }

}
