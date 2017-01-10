<?php

namespace GitRestApi;

class RepositoryDiffTest extends TestCase {

  public static function setUpBeforeClass() {
    RepositoryInitTest::setUpBeforeClass();
  }
  
  public function tearDown() {
    self::$repo->deleteAll();
  }

  public function testDiff() {
    // note that putTree: uploads and does a git add
    self::$repo->putTree('a.txt','A');
    self::$repo->postCommit('A -> a.txt');
    self::$repo->putTree('a.txt','AA');
    self::$repo->postCommit('AA -> a.txt');

    $actual = self::$repo->diff('','HEAD~1');
    $expected='diff --git a/a.txt b/a.txt'.PHP_EOL
      .'index 8c7e5a6..6c376d9 100644'.PHP_EOL
      .'--- a/a.txt'.PHP_EOL
      .'+++ b/a.txt'.PHP_EOL
      .'@@ -1 +1 @@'.PHP_EOL
      .'-A'.PHP_EOL
      .'\ No newline at end of file'.PHP_EOL
      .'+AA'.PHP_EOL
      .'\ No newline at end of file'.PHP_EOL
    ;
    $this->assertEquals($expected,$actual);
  }
}

