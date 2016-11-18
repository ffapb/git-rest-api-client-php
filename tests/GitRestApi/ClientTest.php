<?php

namespace GitRestApi;

class ClientTest extends \PHPUnit_Framework_TestCase {

  protected static $git, $repo, $random;

  // https://phpunit.de/manual/current/en/fixtures.html#fixtures.more-setup-than-teardown
  public static function setUpBeforeClass() {
    self::$git        = new Client('http://localhost:8081');

    $remote = 'https://someone:somepass@github.com/shadiakiki1986/git-data-repo-testDataRepo';
    self::$repo = self::$git->cloneRemote($remote);
    self::$random = substr(str_shuffle(MD5(microtime())), 0, 10);
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

