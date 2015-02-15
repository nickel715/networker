<?php

namespace Networker;

interface ExportInterface extends NetworkInterface
{
    public function userExists($username);
}
