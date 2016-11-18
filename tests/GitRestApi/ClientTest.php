<?php

namespace GitRestApi;

class ClientTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    $this->git        = new Client('http://localhost:8081');
  }

  public function testClone() {
    $remote = 'https://USERNAME:PASSWORD@github.com/shadiakiki1986/git-data-repo-testDataRepo';
    $repo = $this->git->cloneRemote($remote);
    $this->assertNotNull($repo);
  }

  public function testGet() {
    // get already-cloned git repository
    $repo = $this->git->get('git-data-repo-testDataRepo');
    $this->assertNotNull($repo);
  }

}

