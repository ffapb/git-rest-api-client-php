<?php

namespace GitRestApi;

class Client {

  function __construct(string $endpoint) {
    if(!parse_url($endpoint)) {
      throw new \Exception("Invalid endpoint URL: $endpoint");
    }
    // strip trailing slash
    // http://stackoverflow.com/a/3710970/4126114
    $endpoint = rtrim($endpoint,'/');
    $this->endpoint = $endpoint;
  }

  public function path(string $part2) {
    return $this->endpoint.'/'.$part2;
  }

  public function get(string $reponame) {
    // get list of cloned
    $response = \Httpful\Request::get($this->endpoint)->send();

    if(in_array($reponame,$response->body)) {
      return new Repository($this,$reponame);
    }

    return false;
  }

  public static function handleError(\Httpful\Response $response) {
    if(isset($response->body->error)) {
      throw new \Exception($response->body->error);
    }
  }

  public function cloneRemote(string $remote) {
    $reponame = basename($remote);
    $repo = $this->get($reponame);
    if(!!$repo) {
      return $repo;
    }

    // otherwise perform clone
    $response = \Httpful\Request::post(
      $this->path("clone"),
      json_encode(["remote"=>$remote])
    )->sendsJson()->send();

    self::handleError($response);

    return new Repository($this,$response->repo);
  }

}

