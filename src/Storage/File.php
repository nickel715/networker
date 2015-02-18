<?php

namespace Networker\Storage;

use Networker\StorageInterface;

class File implements StorageInterface
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function findAll()
    {
        $all = explode(PHP_EOL, file_get_contents($this->path));
        $all = array_filter($all);
        $all = array_unique($all);
        return $all;
    }

    public function add($user)
    {
        file_put_contents($this->path, $user . PHP_EOL, FILE_APPEND);
    }

    public function addAll(array $users)
    {
        file_put_contents($this->path, implode(PHP_EOL, $users) . PHP_EOL, FILE_APPEND);
    }
}
