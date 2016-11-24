<?php

namespace GitRestApi;

class PrivateTest extends \PHPUnit_Framework_TestCase {

  public function testBitbucket() {
    if(!getenv('PRIVATE_REMOTE')) {
      $this->markTestSkipped('Define env var PRIVATE_REMOTE for this test');
    }
    if(!getenv('PRIVATE_FILE')) {
      $this->markTestSkipped('Define env var PRIVATE_FILE for this test');
    }

    $git        = new Client('http://localhost:8081');
    $repo = $git->cloneRemote(getenv('PRIVATE_REMOTE'));
    $x = $repo->getTree(getenv('PRIVATE_FILE'));
    $this->assertLessThan(count(json_decode($x,true)),10);
  }
 
}

