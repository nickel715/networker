<?php

namespace Networker\Network;

use Networker\ImportInterface;
use Zend\Http\Client as HttpClient;
use Zend\Http\Response;

class Github implements ImportInterface
{
    private $httpClient;
    private $username;

    public function getName()
    {
        return 'Github';
    }

    public function getAll()
    {
        if (!($this->httpClient instanceof HttpClient)) {
            throw new \Exception('Zend\Http\Client required');
        }
        $followers = $this->fetchUsers('followers');
        $following = $this->fetchUsers('following');
        return array_merge($followers, $following);
    }

    private function fetchUsers($type)
    {
        $baseUrl = 'https://api.github.com/users/';
        $client = clone $this->httpClient;
        $client->setUri($baseUrl . $this->username . '/' . $type);
        $response = $client->send();
        return $this->extractUsers($response);
    }

    private function extractUsers(Response $response)
    {
        $response = json_decode($response->getBody());
        $userList = [];
        foreach ($response as $user) {
            $userList[] = $user->login;
        }
        return $userList;
    }

    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }
}
