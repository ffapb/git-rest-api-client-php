<?php
require_once 'vendor/autoload.php';

if(!defined('PASSWORD')) define('PASSWORD','mypassword');

// The below would eventuall end up as an adapter for
// http://www.php-cache.com/en/latest/
// and would replace my GitDataRepo

$base='http://pmo.ffaprivatebank.com:8081/';
$response = \Httpful\Request::get($base)->send();

// try to https://github.com/korya/node-git-rest-api
//\Httpful\Request::post($base."init", json_encode(["repo"=>"new-project"]))->sendsJson()->send();

// clone if not there
if(!in_array('git-data-repo-testDataRepo',$response->body)) {
  \Httpful\Request::post($base."clone", json_encode(["remote"=>"https://github.com/shadiakiki1986/git-data-repo-testDataRepo"]))->sendsJson()->send();
}

// get key
$key = $base."repo/git-data-repo-testDataRepo/tree/bla";
$response = \Httpful\Request::get($key)->send();
if(isset($response->body->error)) {
  throw new \Exception($response->body->error);
}
var_dump('bla content',$response->body);

if(false) {
  // set key
  $file_name_with_full_path = '/tmp/bla';
  file_put_contents($file_name_with_full_path,'another content');
  $cFile = curl_file_create($file_name_with_full_path);
  $params = array('file'=> $cFile);
  //$params = ['file' => '@' . $file_name_with_full_path];

  $response = \Httpful\Request::put($key,$params,\Httpful\Mime::UPLOAD)                  // Build a PUT request...
  //    ->body('another content')             // attach a body/payload...
      ->send();                                   // and finally, fire that thing off!
  var_dump('bla put',$response->body);

  // get key again
  $response = \Httpful\Request::get($key)->send();
  if(isset($response->body->error)) {
    throw new \Exception($response->body->error);
  }
  var_dump('bla get again',$response->body);

  // commit edits to bla
  $response = \Httpful\Request::post($base.'repo/git-data-repo-testDataRepo/commit')                  // Build a PUT request...
      ->sendsJson()
      ->body(json_encode(['message'=>'A commit message']))             // attach a body/payload...
      ->send();                                   // and finally, fire that thing off!
  var_dump('bla commit',$response->body);

  // push commits
  $response = \Httpful\Request::post($base.'repo/git-data-repo-testDataRepo/push')                  // Build a PUT request...
      ->sendsJson()
      ->body(json_encode(['remote'=>'https://shadiakiki1986:'.PASSWORD.'@github.com/shadiakiki1986/git-data-repo-testDataRepo']))             // attach a body/payload...
      ->send();                                   // and finally, fire that thing off!
  var_dump('bla push',$response->body);
}

