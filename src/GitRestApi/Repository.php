<?php

namespace GitRestApi;

use Httpful\Http;

// php class for interfacing with all things /repo/:repo/... in node-git-rest-api server
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

  public function getTree(string $path) {
    return $this->run(Http::GET,'tree',$path);
  }

  public function putTree(string $path, string $value) {
    // save to a temporary file
    $file_name_with_full_path = tempnam(sys_get_temp_dir(), 'FOO');
    file_put_contents($file_name_with_full_path,$value);

    // send PUT request
    return $this->run(Http::PUT,'tree',$path,[],$file_name_with_full_path);
  }

  public function deleteAll() {
    return $this->run(Http::DELETE,'');
  }

  public function deleteKey(string $path) {
    $this->run(Http::DELETE,'tree',$path);
  }

  public function putConfig(string $name, string $value) {
    $params = ['name'=>$name,'value'=>$value];
    return $this->run(Http::PUT,'config',null,$params);
  }

/*
  private function configPutIfNotExists(string $name, string $value) {
    $response = $this->run(Http::GET,'config',null,['name'=>$name]);

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
    $this->appendParams($params,'message',$message);
    if($allowEmpty) $this->appendParams($params,'allow-empty',$allowEmpty);

    //
    return $this->run(Http::POST,'commit',null,$params);
  }

  private function appendParams(array &$params, string $name, string $value) {
    $params=array_merge(
      $params,
      [$name=>$value]
    );
  }

  // push commits
  public function push(string $remote=null,string $branch=null) {
    $params=[];
    if(!is_null($remote)) {
      $this->appendParams($params,'remote',$remote);
    }
    if(!is_null($branch)) {
      $this->appendParams($params,'branch',$branch);
    }

    return $this->run(Http::POST,'push',null,$params);
  }

  public function pull(string $remote=null, string $branch=null) {
    $params=[];
    if(!is_null($remote)) {
      $this->appendParams($params,'remote',$remote);
    }
    if(!is_null($branch)) {
      $this->appendParams($params,'branch',$branch);
    }

    return $this->run(Http::POST,'pull',null,$params);
  }

  public function lsTree(string $path,string $rev=null) {
    $params=[];
    if(!is_null($rev)) {
      $this->appendParams($params,'rev',$rev);
    }

    return $this->run(Http::GET,'ls-tree',$path,$params);
  }

  // method: string from https://github.com/nategood/httpful/blob/master/src/Httpful/Http.php#L11
  private function run(string $method, string $path1, string $path2=null, array $params=[], string $attachment=null) {

    $path = $path1;
    if(!is_null($path2)) {
      $path.='/'.$path2;
    }
    $request = \Httpful\Request::init()
      ->method($method)
      ->uri($this->path($path));

    if(!is_null($attachment)) {
      $request = $request->attach(array('file' => $attachment));
    }

    if(count($params)>0) {
        $request = $request
          ->sendsJson()
          ->body(json_encode($params));
    }
    $response = $request->send();
    Client::handleError($response);
//    if($method=='GET' && $path1=='ls-tree') var_dump($response);
    return $response->body;
  }

}
