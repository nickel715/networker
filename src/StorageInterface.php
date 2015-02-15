<?php

namespace Networker;

interface StorageInterface
{
    public function findAll();
    public function add($user);
    public function addAll(array $users);
}
