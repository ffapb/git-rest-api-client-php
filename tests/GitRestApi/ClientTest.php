<?php

namespace GitRestApi;

class ClientTest extends TestCase {

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

  public function testCloneShallowOk() {
    self::$repo->deleteAll();
    $repo = self::$git->cloneRemote(self::$remote,null,null,1);
    $this->assertNotNull($repo);
    $this->assertInstanceOf(Repository::class,$repo);
  }

  public function testCloneShallowFasterThanDeep() {
    // shallow clone
    self::$repo->deleteAll();
    $startShallow = microtime(true);
    $repo = self::$git->cloneRemote(self::$remote,1);
    $endShallow = microtime(true);

    // deep clone
    self::$repo->deleteAll();
    $startDeep = microtime(true);
    $repo = self::$git->cloneRemote(self::$remote);
    $endDeep = microtime(true);

    // assert deep slower than shallow
    $this->assertLessThan(2,1);
    $this->assertLessThan(
      $endDeep-$startDeep + 1, // 1 second buffer
      $endShallow-$startShallow
    );
  }

}

