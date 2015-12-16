#!/usr/bin/env php
<?php
require 'vendor/autoload.php';
require 'util.php';

function main()
{
    $loop = React\EventLoop\Factory::create();
    $socket = new React\Socket\Server($loop);
    $http = new React\Http\Server($socket, $loop);

    $http->on('request', function ($request, $response) {
        hexdump($request->getQuery());
        $response->writeHead(200, array('Content-Type' => 'text/plain'));
        $response->end("Hello World\n");
    });

    echo "Server running at http://127.0.0.1:1337\n";

    $socket->listen(1337);
    $loop->run();
}

main();
?>
