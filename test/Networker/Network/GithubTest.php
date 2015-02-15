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

    protected function getClientMock()
    {
        return $this->getMock('Zend\Http\Client', ['setUri', 'send']);
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
        $this->sut->getAll('octocat');
    }

    public function testGetAll()
    {
        $clientMock = $this->getClientMock();
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
        $actual = $this->sut->getAll('octocat');

        $expected = ['tekkub','mdo','charliesome','benbalter','muan','jlord'];
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provideUserExists
     */
    public function testUserExists($statusCode, $expected)
    {
        $clientMock = $this->getClientMock();
        $clientMock->expects($this->once())
            ->method('setUri')
            ->with('https://api.github.com/users/octocat');

        $clientMock->expects($this->once())
            ->method('send')
            ->willReturn((new Response)->setStatusCode($statusCode));

        $this->sut->setHttpClient($clientMock);
        $this->assertEquals($expected, $this->sut->userExists('octocat'));
    }

    public function provideUserExists()
    {
        return [
            [200, true],
            [404, false],
        ];
    }
}
