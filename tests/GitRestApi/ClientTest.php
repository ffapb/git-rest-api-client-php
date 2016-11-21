<?php

namespace GitRestApi;

class ClientTest extends GitRestApi_TestCase {

  public static function tearDownAfterClass() {
    self::$repo->deleteAll();
  }

  public function testClone() {
    $this->assertNotNull(self::$repo);
  }

  public function testGet() {
    // get already-cloned git repository
    $repo = self::$git->get('git-data-repo-testDataRepo');
    $this->assertNotNull($repo);
  }

}

