<?php
/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require '../vendor/autoload.php';
$loader->register();

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();
$core = new Framework\Core();

/** @var Response $response */
$response = $core->handle($request);
if (!$response instanceof Response) {
    throw new \ErrorException('Return result must be an instance of Response');
}

$response->send();
