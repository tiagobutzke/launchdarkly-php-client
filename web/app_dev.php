<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Response;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

$loader = require_once __DIR__.'/../app/autoload.php';

Debug::enable();

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
//$kernel->loadClassCache();
$request = Request::createFromGlobals();

$response = $kernel->handle($request);

if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
    require __DIR__.'/../app/seo-redirects.php';
}

$response->send();
$kernel->terminate($request, $response);
