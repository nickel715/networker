<?php

namespace Networker\Network;

use Networker\ExportInterface;
use Zend\Http\Client as HttpClient;

class Pinboard implements ExportInterface
{
    private $httpClient;
    private $baseUrl = 'https://pinboard.in';

    public function getName()
    {
        return 'Pinboard.in';
    }

    public function getUserLink($username)
    {
        return $this->baseUrl . '/u:' . $username;
    }

    public function userExists($username)
    {
        $client = clone $this->httpClient;
        $client->setUri($this->baseUrl . '/u:' . $username);
        $response = $client->send();
        return $response->isSuccess();
    }

    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }
}
