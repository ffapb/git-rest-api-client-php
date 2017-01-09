<?php

namespace GitRestApi;

// repeat same tests as RepositoryCloneTest, but with a repo from an 'init' instead of a 'clone'
class RepositoryInitTest extends RepositoryCloneTest {

  public static function setUpBeforeClass() {
    self::$git        = new Client('http://localhost:8081');
    self::$repo = self::$git->get('git-init');
    if(!self::$repo) {
      self::$repo = self::$git->init('git-init');
    }

    self::$random = substr(str_shuffle(MD5(microtime())), 0, 10);
  }

}
