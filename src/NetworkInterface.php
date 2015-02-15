<?php

namespace Networker;

interface NetworkInterface
{
    public function getName();
    public function getUserLink($username);
}
