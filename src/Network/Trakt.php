<?php

namespace Networker\Network;

use Networker\ExportInterface;
use Zend\Http\Client as HttpClient;

class Trakt implements ExportInterface
{
    private $httpClient;
    private $baseUrl = 'https://trakt.tv';

    public function getName()
    {
        return 'trakt.tv';
    }

    public function getUserLink($username)
    {
        return $this->baseUrl . '/users/' . $username;
    }

    public function userExists($username)
    {
        $client = clone $this->httpClient;
        $client->setUri($this->baseUrl . '/users/' . $username);
        $response = $client->send();
        if ($response->getStatusCode() == 404) {
            return false;
        } elseif ($response->isSuccess()) {
            return true;
        }
        throw new \Exception('Unkown response ' . $response->renderStatusLine());
    }

    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }
}
