# git-rest-api-client-php

[![Build Status](https://travis-ci.org/shadiakiki1986/git-rest-api-client-php.svg?branch=master)](https://travis-ci.org/shadiakiki1986/git-rest-api-client-php)

PHP client interfacing with RESTful API from server running [node-git-rest-api](https://github.com/korya/node-git-rest-api), as dockerized in [docker-node-git-rest-api](https://github.com/shadiakiki1986/docker-node-git-rest-api)

### Install

Published on [packagist](https://packagist.org/packages/shadiakiki1986/git-rest-api-client)

```bash
composer require shadiakiki1986/git-rest-api-client
```

### Use

Launch a `node-git-rest-api` server

```bash
docker run -p 8081:8081 -it shadiakiki1986/docker-node-git-rest-api
```

To make changes to a file in a repository

```php
$git        = new GitRestApi\Client('http://localhost:8081');
// clone a git repository on github
$remote = 'https://USERNAME:PASSWORD@github.com/shadiakiki1986/git-data-repo-testDataRepo';
$repo = $git->cloneRemote($remote);
// update a file called 'filename' in the repository
$repo->put('filename','some content');
// commit the changes
$repo->commit('a new commit message');
// push to the remote
$repo->push();
```

To pull changes and get contents of a file
```php
$git        = new GitRestApi\Client();
// get already-cloned git repository
$repo = $git->get('git-data-repo-testDataRepo');
// pull changes
$repo->pull();
// get contents of file
$repo->get('filename');
```

## Testing
Launch a `node-git-rest-api` server

```bash
docker run -p 8081:8081 -it shadiakiki1986/docker-node-git-rest-api
```

`composer test` will test everything except a successful push.

```
export GitRestApiTestUrl=https://shadiakiki1986:veggiepizza@github.com/shadiakiki1986/git-data-repo-testDataRepo
composer test
```
will test everything, including the successful push.

## Travis testing
Check [git-data-repo](https://github.com/shadiakiki1986/git-data-repo)

## TODO
2016-11-19
* push is not pushing to github
* travis not passing
