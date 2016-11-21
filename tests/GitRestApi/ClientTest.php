<?php

namespace GitRestApi;

class ClientTest extends \PHPUnit_Framework_TestCase {

  protected static $git, $remote, $repo, $random;

  // https://phpunit.de/manual/current/en/fixtures.html#fixtures.more-setup-than-teardown
  public static function setUpBeforeClass() {
    self::$git        = new Client('http://localhost:8081');

    self::$remote = 'https://someone:somepass@github.com/shadiakiki1986/git-data-repo-testDataRepo';
    self::$repo = self::$git->cloneRemote(self::$remote);
    self::$random = substr(str_shuffle(MD5(microtime())), 0, 10);
  }

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

