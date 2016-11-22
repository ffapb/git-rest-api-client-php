<?php

namespace GitRestApi;

use Httpful\Http;

class RequestTest extends \PHPUnit_Framework_TestCase {

  public function testUrl() {
    // basic
    $req  = new Request(Http::GET,'http://endpoint:1234');
    $this->assertEquals('http://endpoint:1234',$req->url());

    // drops trailing slash
    $req  = new Request(Http::GET,'http://endpoint:1234/');
    $this->assertEquals('http://endpoint:1234', $req->url());

    // more function arguments
    $req = new Request(Http::GET,'http://endpoint:1234',[],'action');
    $this->assertEquals('http://endpoint:1234/action',$req->url());

    $req = new Request(Http::GET,'http://endpoint:1234',[],'action','reponame');
    $this->assertEquals('http://endpoint:1234/repo/reponame/action',$req->url());

    $req = new Request(Http::GET,'http://endpoint:1234',[],'action','reponame','path/to/something');
    $this->assertEquals('http://endpoint:1234/repo/reponame/action/path/to/something',$req->url());
  }

}

