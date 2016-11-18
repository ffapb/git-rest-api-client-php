<?php

namespace GitRestApi;

class RepositoryTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    $git        = new Client('http://localhost:8081');
    $this->repo = $git->cloneRemote('git-data-repo-testDataRepo');
    $this->random = substr(str_shuffle(MD5(microtime())), 0, 10);
  }

  public function testGetInexistant() {
    // fails
    $this->repo->get($this->random);
  }

  public function testGetOk() {
    $actual = $this->repo->get('bla');
    $this->assertNotNull($actual);
  }

  public function testPutCommitPush() {
    // update a file called 'filename' in the repository
    $key = 'filename';

    // with random content
    // http://stackoverflow.com/a/4356295/4126114
    $this->repo->put($key,$this->random);

    // confirm get
    $actual = $this->repo->get($key);
    $this->assertEquals($actual,$this->random);

    // commit the changes
    $this->repo->commit('a new test commit message');

    // push to the remote
    $this->repo->push();
  }

  public function testPullGet() {
    // pull changes
    $this->repo->pull();
    // get contents of file
    $actual = $this->repo->get('filename');
    $this->assertEquals($actual,$this->random);
  }

}

