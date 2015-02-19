<?php

namespace Networker\Test\Network;

use Networker\Network\Pinboard;
use Zend\Http\Client as HttpClient;
use Zend\Http\Response;

class PinboardTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sut = new Pinboard;
    }

    protected function getClientMock()
    {
        return $this->getMock('Zend\Http\Client', ['setUri', 'send']);
    }

    public function testGetName()
    {
        $this->assertEquals('Pinboard.in', $this->sut->getName());
    }

    public function testGetUserLink()
    {
        $this->assertEquals('https://pinboard.in/u:pinboard', $this->sut->getUserLink('pinboard')); // TODO
    }

    /**
     * @dataProvider provideUserExists
     */
    public function testUserExists($statusCode, $expected)
    {
        $clientMock = $this->getClientMock();
        $clientMock->expects($this->once())
            ->method('setUri')
            ->with('https://pinboard.in/u:pinboard');

        $clientMock->expects($this->once())
            ->method('send')
            ->willReturn((new Response)->setStatusCode($statusCode));

        $this->sut->setHttpClient($clientMock);
        $this->assertEquals($expected, $this->sut->userExists('pinboard'));
    }

    public function provideUserExists()
    {
        return [
            [200, true],
            [404, false],
        ];
    }
}
