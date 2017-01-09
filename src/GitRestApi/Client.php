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

  public function init(string $reponame, bool $bare=false, bool $shared=false) {
    $params = ['repo'=>$reponame];
    if($bare) self::appendParams($params,'bare',$bare);
    if($shared) self::appendParams($params,'shared',$shared);

    $req = new Request(Http::POST, $this->endpoint, $params, 'init');
    $response = $req->send();

    return new Repository($this,$response->repo);
  }

  public function cloneRemote(string $remote, int $depth=null, string $repo=null, string $bare=null) {
    $reponame = basename($remote);
    $existing = $this->get($reponame);
    if(!!$existing) {
      return $existing;
    }

    $params=["remote"=>$remote];
    if(!is_null($depth)) {
      Repository::appendParams($params,'depth',$depth);
    }
    if(!is_null($repo)) {
      Repository::appendParams($params,'repo',$repo);
    }
    if(!is_null($bare)) {
      Repository::appendParams($params,'bare',$bare);
    }

    // otherwise perform clone
    $req = new Request(Http::POST, $this->endpoint, $params, 'clone');
    $response = $req->send();

    return new Repository($this,$response->repo);
  }

}

