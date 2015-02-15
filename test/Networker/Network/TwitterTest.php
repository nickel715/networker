<?php

namespace Networker\Test\Network;

use Networker\Network\Twitter;
use TwitterOAuth\Exception\TwitterException;

class TwitterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sut = new Twitter;
        $this->twitterMock = $this->getMockBuilder('TwitterOAuth\Auth\SingleUserAuth')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->sut->setTwitter($this->twitterMock);
    }

    public function testGetName()
    {
        $this->assertEquals('Twitter', $this->sut->getName());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Twitter required
     */
    public function testExecptionIfClientMissing()
    {
        (new Twitter)->getAll('theSeanCook');
    }

    public function testGetAllMultiPage()
    {
        $this->twitterMock->expects($this->at(0))
            ->method('get')
            ->with('followers/list', ['cursor' => -1, 'count' => 200, 'screen_name' => 'theSeanCook'])
            ->willReturn(['users' => [['screen_name' => 'Alice'], ['screen_name' => 'Bob']], 'next_cursor' => 12345]);

        $this->twitterMock->expects($this->at(1))
            ->method('get')
            ->with('followers/list', ['cursor' => 12345, 'count' => 200, 'screen_name' => 'theSeanCook'])
            ->willReturn(['users' => [['screen_name' => 'Carol'], ['screen_name' => 'Dave']], 'next_cursor' => 0]);

        $this->twitterMock->expects($this->at(2))
            ->method('get')
            ->with('friends/list', ['cursor' => -1, 'count' => 200, 'screen_name' => 'theSeanCook'])
            ->willReturn(['users' => [], 'next_cursor' => 0]);

        $actual = $this->sut->getAll('theSeanCook');
        $this->assertEquals(['Alice', 'Bob', 'Carol', 'Dave'], $actual);
    }

    public function testGetAllFollwersAndFriends()
    {
        $this->twitterMock->expects($this->at(0))
            ->method('get')
            ->with('followers/list', ['cursor' => -1, 'count' => 200, 'screen_name' => 'theSeanCook'])
            ->willReturn(['users' => [['screen_name' => 'Alice'], ['screen_name' => 'Bob']], 'next_cursor' => 0]);

        $this->twitterMock->expects($this->at(1))
            ->method('get')
            ->with('friends/list', ['cursor' => -1, 'count' => 200, 'screen_name' => 'theSeanCook'])
            ->willReturn(['users' => [['screen_name' => 'Carol'], ['screen_name' => 'Dave']], 'next_cursor' => 0]);

        $actual = $this->sut->getAll('theSeanCook');
        $this->assertEquals(['Alice', 'Bob', 'Carol', 'Dave'], $actual);
    }

    public function testUserExists()
    {
        $this->twitterMock->expects($this->once())
            ->method('get')
            ->with('users/show', ['screen_name' => 'theSeanCook']);
        $this->assertTrue($this->sut->userExists('theSeanCook'));
    }

    public function testUserNotExists()
    {
        $this->twitterMock->expects($this->once())
            ->method('get')
            ->with('users/show', ['screen_name' => 'notExisting'])
            ->will($this->throwException(new TwitterException('', 34)));
        $this->assertFalse($this->sut->userExists('notExisting'));
    }
}
