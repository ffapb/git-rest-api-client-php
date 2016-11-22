<?php

namespace GitRestApi;

use Httpful\Http;

// php class for interfacing with all things /repo/:repo/... in node-git-rest-api server
class Repository {

  function __construct(Client $client, string $reponame) {
    $this->client = $client;
    $this->reponame = $reponame;
  }

  private function path(string $action) {
    return $this->client->path(
      'repo/'.$this->reponame.'/'.$action
    );
  }

  public function getTree(string $path) {
    return $this->run(Http::GET,'tree',[],$path);
  }

  public function putTree(string $path, string $value) {
    // save to a temporary file
    $file_name_with_full_path = tempnam(sys_get_temp_dir(), 'FOO');
    file_put_contents($file_name_with_full_path,$value);

    // send PUT request
    return $this->run(Http::PUT,'tree',[],$path,$file_name_with_full_path);
  }

  public function deleteAll() {
    return $this->run(Http::DELETE,'');
  }

  public function deleteKey(string $path) {
    $this->run(Http::DELETE,'tree',$path);
  }

  public function putConfig(string $name, string $value) {
    $params = ['name'=>$name,'value'=>$value];
    return $this->run(Http::PUT,'config',$params);
  }

/*
  private function configPutIfNotExists(string $name, string $value) {
    $response = $this->run(Http::GET,'config',['name'=>$name]);

    if(!in_array($name,$response)) {
      if(is_null($userName)) {
        throw new \Exception('Need to config repo '.$name.'. Please pass it to commit(...)');
      }
      $this->updateConfig($name,$value);
    }
  }
*/

/*
  public function postConfig
 string $userName=null, string $userEmail=null)
    // check if a user name and email are configured
    if(!is_null($userName)) {
      $this->updateConfigIfNotExists('user.name',$userName);
    }
    if(!is_null($userEmail)) {
      $this->updateConfigIfNotExists('user.email',$userEmail);
    }
*/

  public function postCommit(string $message, bool $allowEmpty=false) {
    $params = [];
    self::appendParams($params,'message',$message);
    if($allowEmpty) self::appendParams($params,'allow-empty',$allowEmpty);

    //
    return $this->run(Http::POST,'commit',$params);
  }

  public static function appendParams(array &$params, string $name, string $value) {
    $params=array_merge(
      $params,
      [$name=>$value]
    );
  }

  // push commits
  public function push(string $remote=null,string $branch=null) {
    $params=[];
    if(!is_null($remote)) {
      self::appendParams($params,'remote',$remote);
    }
    if(!is_null($branch)) {
      self::appendParams($params,'branch',$branch);
    }

    return $this->run(Http::POST,'push',$params);
  }

  public function pull(string $remote=null, string $branch=null) {
    $params=[];
    if(!is_null($remote)) {
      self::appendParams($params,'remote',$remote);
    }
    if(!is_null($branch)) {
      self::appendParams($params,'branch',$branch);
    }

    return $this->run(Http::POST,'pull',$params);
  }

  public function lsTree(string $path,string $rev=null) {
    $params=[];
    if(!is_null($rev)) {
      self::appendParams($params,'rev',$rev);
    }

    return $this->run(Http::GET,'ls-tree',$params,$path);
  }

  private function run(string $method, string $action=null, array $params=[], string $path=null, string $attachment=null) {
    $req = new Request($method, $this->client->endpoint, $params, $action, $this->reponame, $path, $attachment);
    return $req->send();
  }

}
