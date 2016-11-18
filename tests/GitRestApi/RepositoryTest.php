<?php

namespace GitRestApi;

class RepositoryTest extends \PHPUnit_Framework_TestCase {

  protected static $repo, $random;

  // https://phpunit.de/manual/current/en/fixtures.html#fixtures.more-setup-than-teardown
  public static function setUpBeforeClass() {
    $git        = new Client('http://localhost:8081');
    self::$repo = $git->cloneRemote('git-data-repo-testDataRepo');
    self::$random = substr(str_shuffle(MD5(microtime())), 0, 10);
  }

  /**
   * @expectedException Exception
   */
  public function testGetInexistant() {
    // fails
    self::$repo->get(self::$random);
  }

  public function testGetOk() {
    $actual = self::$repo->get('bla');
    $this->assertNotNull($actual);
  }

  public function testPutCommitPush() {
    // update a file called 'filename' in the repository
    $key = 'filename';

    // with random content
    // http://stackoverflow.com/a/4356295/4126114
    self::$repo->put($key,self::$random);

    // confirm get
    $actual = self::$repo->get($key);
    $this->assertEquals($actual,self::$random);

    // commit the changes
    self::$repo->commit('a new test commit message');

    // push to the remote
    self::$repo->push();
  }

  /**
   * @depends testPutCommitPush
   */
  public function testPullGet() {
    // pull changes
    self::$repo->pull();
    // get contents of file
    $actual = self::$repo->get('filename');
    $this->assertEquals($actual,self::$random);
  }

}

