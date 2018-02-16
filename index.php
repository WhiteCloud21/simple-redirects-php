<?php

require_once __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config.php';
$redirects = require __DIR__ . '/redirects.php';

$loop = React\EventLoop\Factory::create();

$server = new React\Http\Server(function (Psr\Http\Message\ServerRequestInterface $request) use ($redirects) {
    $newUri = $request->getUri()->withScheme('https');
    if (isset($redirects[$newUri->getPath()])) {
        $newUri = $newUri->withPath($redirects[$newUri->getPath()]);
    }

    return new React\Http\Response(
        301,
        ['Location' => $newUri, 'X-Powered-By' => 'SimpleRedirects']
    );
});

$socket = new React\Socket\Server($config['listen'], $loop);
$server->listen($socket);

echo "Server running at {$config['listen']}\n";

$loop->run();