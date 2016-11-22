<?php

namespace GitRestApi;

use Httpful\Http;

class Client {

  function __construct(string $endpoint) {
    $this->endpoint = $endpoint;
  }

  public function get(string $reponame) {
    // get list of cloned
    $req = new Request(Http::GET, $this->endpoint);
    $response = $req->send();

    if(in_array($reponame,$response)) {
      return new Repository($this,$reponame);
    }

    return false;
  }

  public function cloneRemote(string $remote, string $repo=null, string $bare=null, int $depth=null) {
    $reponame = basename($remote);
    $repo = $this->get($reponame);
    if(!!$repo) {
      return $repo;
    }

    // otherwise perform clone
    $req = new Request(Http::POST, $this->endpoint, ["remote"=>$remote], 'clone');
    $response = $req->send();

    return new Repository($this,$response->repo);
  }

}

