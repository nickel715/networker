<?php

namespace Networker\Test\Network;

use Networker\Network\Github;
use Zend\Http\Client as HttpClient;
use Zend\Http\Response;

class GithubTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sut = new Github;
    }

    public function testGetName()
    {
        $this->assertEquals('Github', $this->sut->getName());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Client required
     */
    public function testExecptionIfClientMissing()
    {
        $this->sut->getAll();
    }

    public function testGetAll()
    {
        $clientMock = $this->getMock('Zend\Http\Client', ['setUri', 'send']);
        $clientMock->expects($this->exactly(2))
            ->method('setUri')
            ->withConsecutive(
                ['https://api.github.com/users/octocat/followers'],
                ['https://api.github.com/users/octocat/following']
            );

        $responseFollowers = new Response;
        $responseFollowers->setContent('[{"login": "tekkub"},{"login": "mdo"},{"login": "charliesome"}]');
        $responseFollowing = new Response;
        $responseFollowing->setContent('[{"login": "benbalter"},{"login": "muan"},{"login": "jlord"}]');
        $clientMock->expects($this->exactly(2))
            ->method('send')
            ->will($this->onConsecutiveCalls($responseFollowers, $responseFollowing));

        $this->sut->setHttpClient($clientMock);
        $this->sut->setUsername('octocat');
        $actual = $this->sut->getAll();
        $expected = ['tekkub','mdo','charliesome','benbalter','muan','jlord'];
        $this->assertEquals($expected, $actual);
    }
}
