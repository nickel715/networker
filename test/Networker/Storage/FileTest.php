<?php

namespace Networker\Test\Storage;

use Networker\Storage\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    private $userList;
    private $userListRaw;
    private $file = 'users.txt';
    public function setUp()
    {
        $this->cleanupPersistance();
        $this->sut = new File($this->file);
        $this->userList = ['octocat', 'tekkub', 'mdo'];
        $this->userListRaw = 'octocat' . PHP_EOL . 'tekkub' . PHP_EOL . 'mdo' . PHP_EOL;
    }

    public function tearDown()
    {
        $this->cleanupPersistance();
    }

    public function cleanupPersistance()
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public function testFindAll()
    {
        file_put_contents($this->file, $this->userListRaw);
        $this->assertEquals($this->userList, $this->sut->findAll());
    }

    public function testAdd()
    {
        file_put_contents($this->file, 'octocat' . PHP_EOL);
        $this->sut->add('tekkub');
        $this->sut->add('mdo');
        $this->assertEquals($this->userListRaw, file_get_contents($this->file));
    }

    public function testAll()
    {
        $this->sut->addAll($this->userList);
        $this->assertEquals($this->userListRaw, file_get_contents($this->file));
    }


    public function testAllExisting()
    {
        file_put_contents($this->file, 'octocat' . PHP_EOL);
        array_shift($this->userList);
        $this->sut->addAll($this->userList);
        $this->assertEquals($this->userListRaw, file_get_contents($this->file));
    }

    public function testFindAllNoDuplicates()
    {
        file_put_contents($this->file, $this->userListRaw . 'octocat' . PHP_EOL);
        $this->assertEquals($this->userList, $this->sut->findAll());
    }
}
