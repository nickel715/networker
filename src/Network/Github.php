<?php

namespace Networker\Network;

use Networker\ImportInterface;
use Networker\ExportInterface;
use Zend\Http\Client as HttpClient;
use Zend\Http\Response;

class Github implements ImportInterface, ExportInterface
{
    private $httpClient;
    private $baseUrl = 'https://api.github.com/';

    public function getName()
    {
        return 'Github';
    }

    public function getUserLink($username)
    {
        return 'https://github.com/' . $username;
    }

    public function getAll($username)
    {
        if (!($this->httpClient instanceof HttpClient)) {
            throw new \Exception('Zend\Http\Client required');
        }
        $followers = $this->fetchUsers('followers', $username);
        $following = $this->fetchUsers('following', $username);
        return array_merge($followers, $following);
    }

    public function userExists($username = null)
    {
        $client = clone $this->httpClient;
        $client->setUri($this->baseUrl . 'users/' . $username);
        $response = $client->send();
        if (!($response->isSuccess() || $response->getStatusCode() == 404)) {
            throw new \Exception('Unexpected response ' . $response->getStatusCode() . ' ' . $response->getBody());
        }
        return $response->isSuccess();
    }

    private function fetchUsers($type, $username)
    {
        $client = clone $this->httpClient;
        $client->setUri($this->baseUrl . 'users/' . $username . '/' . $type);
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
}
