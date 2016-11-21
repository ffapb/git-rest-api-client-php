<?php

namespace GitRestApi;

class RepositoryTest extends TestCase {

  /**
   * @expectedException Exception
   */
  public function testGetTreeInexistant() {
    $this->markTestIncomplete('Pending understand ls-tree in node-git-rest-api');

    // also confirm with lsTree
    $exists = self::$repo->lsTree(self::$random);
    $this->assertFalse($exists);

    // fails
    self::$repo->getTree(self::$random);
  }

  public function testGetTreeOk() {
    $this->markTestIncomplete('Pending understand ls-tree in node-git-rest-api');

    $actual = self::$repo->getTree('bla');
    $this->assertNotNull($actual);

    // also confirm with lsTree
    $exists = self::$repo->lsTree('bla');
    $this->assertTrue($exists);
  }

  public function testPutTree() {
    // update a file called 'filename' in the repository
    $key = 'filename';

    // with random content
    // http://stackoverflow.com/a/4356295/4126114
    self::$repo->putTree($key,self::$random);

    // confirm with getTree
    $actual = self::$repo->getTree($key);
    $this->assertEquals($actual,self::$random);

    // also confirm with lsTree
    // skipped until lsTree understood
//    $exists = self::$repo->lsTree($key);
//    $this->assertTrue($exists);
  }

  /**
   * @depends testPutTree
   * @expectedException Exception
   */
  public function testCommitFail() {
    // commit the changes
    self::$repo->postCommit('a new test commit message');
  }

  /**
   * @depends testCommitFail
   */
  public function testCommitOk() {
    // config setting user.name and user.email config
    self::$repo->putConfig('user.name','Shadi Akiki phpunit');
    self::$repo->putConfig('user.email','shadiakiki1986@gmail.com');

    // commit the changes
    self::$repo->postCommit('a new test commit message');
  }


  /**
   * @depends testCommitOk
   * @expectedException Exception
   */
  public function testPushFail() {
    // fails because the remote URL in ClientTest does not include credentials
    $response=self::$repo->push();
    var_dump($response);
  }

  /**
   * @depends testPushFail
   */
  public function testPushOk() {
    $URL=getenv('GitRestApiTestUrl');
    if(!$URL) {
      $this->markTestSkipped('no proper url defined.. skipping');
    }

    self::$repo->push($URL);

    // delete and reclone to verify push was ok
    self::$repo->deleteAll();
    self::$repo = self::$git->cloneRemote(self::$remote);
    $actual = self::$repo->getTree('filename');
    $this->assertEquals($actual,self::$random);
  }

  /**
   * @depends testPushOk
   */
  public function testPullGetTree() {
    // pull changes
    self::$repo->pull();
    // get contents of file
    $actual = self::$repo->getTree('filename');
    $this->assertEquals($actual,self::$random);
  }

  /**
   * @depends testPullGetTree
   * @expectedException Exception
   */
  public function testDeleteKey() {
    self::$repo->deleteKey('filename');
    self::$repo->push();
    self::$repo->pull();
    self::$repo->getTree('filename');
  }

}

