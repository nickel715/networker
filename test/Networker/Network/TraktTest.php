<?php

namespace Networker\Test\Network;

use Networker\Network\Trakt;
use Zend\Http\Client as HttpClient;
use Zend\Http\Response;

class TraktTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sut = new Trakt;
    }

    protected function getClientMock()
    {
        return $this->getMock('Zend\Http\Client', ['setUri', 'send']);
    }

    public function testGetName()
    {
        $this->assertEquals('trakt.tv', $this->sut->getName());
    }

    public function testGetUserLink()
    {
        $this->assertEquals('https://trakt.tv/users/sean', $this->sut->getUserLink('sean')); // TODO
    }

    /**
     * @dataProvider provideUserExists
     */
    public function testUserExists($statusCode, $expected)
    {
        $clientMock = $this->getClientMock();
        $clientMock->expects($this->once())
            ->method('setUri')
            ->with('https://trakt.tv/users/sean');
        $clientMock->expects($this->once())
            ->method('send')
            ->willReturn((new Response)->setStatusCode($statusCode));

        $this->sut->setHttpClient($clientMock);
        $this->assertEquals($expected, $this->sut->userExists('sean'));
    }

    public function provideUserExists()
    {
        return [
            [200, true],
            [404, false],
        ];
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Unkown response
     */
    public function testUserExistsUnkownStatusCode()
    {
        $this->testUserExists(408, false);
    }
}
