# Networker
[![Build Status](https://travis-ci.org/nickel715/networker.svg)](https://travis-ci.org/nickel715/networker)
[![Coverage Status](https://coveralls.io/repos/nickel715/networker/badge.svg?branch=master)](https://coveralls.io/r/nickel715/networker?branch=master)

Some websites does not provides a feature to find followers youe follow on other networks like twitter.

## Features

* Get users you are following or followers you
* Store userslist
* Check if users exists on network

## Supported networks

* Twitter
* Github
* Pinboard.in
* Trakt.tv

## Code example

```php
$username = 'nickel715';

$credentials = array(
    'consumer_key'       => '',
    'consumer_secret'    => '',
    'oauth_token'        => '',
    'oauth_token_secret' => '',
);

$twitterAuth = new \TwitterOAuth\Auth\SingleUserAuth($credentials, new \TwitterOAuth\Serializer\ArraySerializer());

$importNetwork = new Network\Twitter;
$importNetwork->setTwitter($twitterAuth);

$userList = $importNetwork->getAll($username);
$storage = new Storage\File('userlist.txt');
$storage->addAll($userList);

$exportNetwork = new Network\Pinboard;
$exportNetwork->setHttpClient(new \Zend\Http\Client);

foreach ($storage->findAll() as $user) {
    if ($exportNetwork->userExists($user)) {
        echo $exportNetwork->getUserLink($user), PHP_EOL;
    }
}
```
