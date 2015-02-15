<?php

namespace Networker\Network;

use Networker\ImportInterface;
use Networker\ExportInterface;
use TwitterOAuth\Auth\SingleUserAuth;
use TwitterOAuth\Exception\TwitterException;

class Twitter implements ImportInterface, ExportInterface
{
    private $twitter;

    public function getName()
    {
        return 'Twitter';
    }

    public function getAll($username)
    {
        if (!($this->twitter instanceof SingleUserAuth)) {
            throw new \Exception('Twitter required');
        }
        $followers = $this->fetchUsers('followers', $username);
        $friends   = $this->fetchUsers('friends', $username);
        return array_merge($followers, $friends);
    }

    public function userExists($username)
    {
        try {
            $this->twitter->get('users/show', ['screen_name' => $username]);
            return true;
        } catch (TwitterException $e) {
            return false;
        }
    }

    private function fetchUsers($type, $username)
    {
        $cursor = -1;
        $users = [];
        do {
            $params = ['cursor' => $cursor, 'count' => 200, 'screen_name' => $username];
            $response = $this->twitter->get($type . '/list', $params);
            foreach ($response['users'] as $user) {
                $users[] = $user['screen_name'];
            }
            $cursor = $response['next_cursor'];
        } while ($cursor != 0);
        return $users;
    }

    public function setTwitter(SingleUserAuth $twitter)
    {
        $this->twitter = $twitter;
    }
}
