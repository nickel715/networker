<?php

namespace Networker;

interface StorageInterface
{
    public function findAll();
    public function create(User $user);
}
