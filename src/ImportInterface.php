<?php

namespace Networker;

interface ImportInterface extends NetworkInterface
{
    public function getAll($username);
}
