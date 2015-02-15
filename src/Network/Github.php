<?php

namespace Networker\Network;

use Networker\ImportInterface;
use Networker\ExportInterface;
use Zend\Http\Client as HttpClient;
use Zend\Http\Response;

class Github implements ImportInterface, ExportInterface
{
    private $httpClient;
    private $username;
    private $baseUrl = 'https://api.github.com/';

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

    public function userExists($username = null)
    {
        if (!is_string($username)) {
            $username = $this->username;
        }
        $client = clone $this->httpClient;
        $client->setUri($this->baseUrl . 'users/' . $username);
        return $client->send()->isSuccess();
    }

    private function fetchUsers($type)
    {
        $client = clone $this->httpClient;
        $client->setUri($this->baseUrl . 'users/' . $this->username . '/' . $type);
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
