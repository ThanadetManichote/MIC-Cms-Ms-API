<?php

use Phalcon\Mvc\Router\Group as RouterGroup;

$router->removeExtraSlashes(true);

$router->setDefaults(array(
    'namespace'  => 'App\Controllers',
    'controller' => 'error',
    'action'     => 'page404'
));

//==========Route for api==========
$api = new RouterGroup(array(
    'namespace' => 'App\Controllers'
));
//==== Start : Schema Section ====//
$api->addGet('/schema', [
    'controller' => 'schema',
    'action'     => 'getSchema',
]);
//==== End : Schema Section ====//
//==== Start : Content Section ====//
$api->addGet('/content', [
    'controller' => 'content',
    'action'     => 'getContent',
]);
$api->addPost('/content', [
    'controller' => 'content',
    'action'     => 'postContent',
]);
//==== End : Content Section ====//

















//==== Start : User Section ====//
$api->addGet('/user', [
    'controller' => 'user',
    'action'     => 'getUser',
]);

$api->addGet('/user/detail', [
    'controller' => 'user',
    'action'     => 'getUserDetail',
]);

$api->addPost('/user', [
    'controller' => 'user',
    'action'     => 'postCreate',
]);

$api->addPut('/user/:params', [
    'controller' => 'user',
    'action'     => 'putUpdate',
    'params'     => 1
]);

$api->addDelete('/user/:params', [
    'controller' => 'user',
    'action'     => 'deleteUser',
    'params'     => 1
]);

$api->addPost('/user/login', [
    'controller' => 'user',
    'action'     => 'postLogin',
]);

$api->addPut('/user/:params/change/password', [
    'controller' => 'user',
    'action'     => 'putChangepassword',
    'params'     => 1
]);
//==== End : User Section ====//

$router->mount($api);

return $router;
