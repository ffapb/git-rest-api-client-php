<?php

namespace GitRestApi;

class RepositoryTest extends ClientTest {

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

  public function testPut() {
    // update a file called 'filename' in the repository
    $key = 'filename';

    // with random content
    // http://stackoverflow.com/a/4356295/4126114
    self::$repo->put($key,self::$random);

    // confirm get
    $actual = self::$repo->get($key);
    $this->assertEquals($actual,self::$random);
  }

  /**
   * @depends testPut
   * @expectedException Exception
   */
  public function testCommitFail() {
    // commit the changes, but fail without setting user.name and user.email config
    self::$repo->commit('a new test commit message');
  }

  /**
   * @depends testCommitFail
   * @expectedException Exception
   */
  public function testCommitOk() {
    // config setting user.name and user.email config
    self::$repo->configPut('user.name','Shadi Akiki phpunit');
    self::$repo->configPut('user.email','shadiakiki1986@gmail.com');

    // commit the changes
    self::$repo->commit('a new test commit message');
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
  }

  /**
   * @depends testPushOk
   */
  public function testPullGet() {
    // pull changes
    self::$repo->pull();
    // get contents of file
    $actual = self::$repo->get('filename');
    $this->assertEquals($actual,self::$random);
  }

  /**
   * @depends testPullGet
   * @expectedException Exception
   */
  public function testDeleteKey() {
    self::$repo->deleteKey('filename');
    self::$repo->push();
    self::$repo->pull();
    self::$repo->get('filename');
  }

  /**
   * @depends testDeleteKey
   */
  public function testDeleteAll() {
    self::$repo->deleteAll();
  }

}

