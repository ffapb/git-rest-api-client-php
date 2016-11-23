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
   */
  public function testDeleteTree() {
    self::$repo->deleteTree('filename');
    try {
      self::$repo->getTree('filename');
      $this->assertTrue(false);
    } catch(\Exception $e) {
      $this->assertTrue(true);
    }

    self::$repo->putTree('filename',self::$random);
  }

  /**
   * @depends testDeleteTree
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

  public function testLogLong() {
    $long = self::$repo->log();
    $this->assertLessThan(2,1);
    $this->assertLessThan(count($long),0);
  }

  public function testLogShort() {
    $short = self::$repo->log("-1");
    $this->assertEquals(1,count($short));

    // sha1
    $short = array_values($short)[0];
    $this->assertEquals(40,strlen($short->sha1));

    // commitDate, e.g. Mon Jun 27 12:14:50 2016 +0300
    $date = \DateTime::createFromFormat('!D M d H:i:s Y O',$short->commitDate);
    $this->assertInstanceOf(\DateTime::class, $date);
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

}

