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
    if(isset($response->body->error)) {
      throw new \Exception($response->body->error);
    }
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
    return $response->body;
  }

  public function commit(string $message) {
    $response = \Httpful\Request::post(
      $this->path('commit')
    )   ->sendsJson()
        ->body(json_encode(['message'=>$message]))
        ->send();
    return $response->body;
  }

  // push commits
  public function push() {
    $response = \Httpful\Request::post(
      $this->path('push')
    )   ->sendsJson()
        ->body(
          json_encode(
            ['remote'=>$this->client->endpoint]
          )
        )
        ->send();
    return $response->body;
  }

  public function pull() {
    $response = \Httpful\Request::post(
      $this->path('push')
    )   ->sendsJson()
        ->body(
          json_encode(
            ['remote'=>$this->client->endpoint]
          )
        )
        ->send();
    return $response->body;
  }

}
