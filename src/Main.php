#!/usr/bin/env php
<?php
require 'vendor/autoload.php';
require 'Methods.php';
require 'Storage.php';

class Request {
    public $request;
    public $response;
    public $contentLength;

    private $rawInputJson;
    function __construct($rq, $rp) {
        $this->request = $rq;
        $this->response = $rp;
    }

    function abort($errcode, $reason) {
        $this->sendAndClose($errcode, $reason, NULL);
    }

    function done($data) {
        $this->sendAndClose(200, "OK", $data);
    }

    function addInput($data) {
        $this->rawInputJson .= $data;
    }

    function readyToProcess() {
        return strlen($this->rawInputJson) >= $this->contentLength;
    }

    function parseInput() {
        return json_decode($this->rawInputJson);
    }

    private function sendAndClose($code, $reason, $data) {
        $json = new stdClass();
        $json->code = $code;
        $json->reason = $reason;
        if ($data)
            $json->results = $data;

        $this->response->writeHead(200, array('Content-Type' => 'text/plain'));
        $this->response->end(json_encode($json));
    }
}

function main() {
    $loop = React\EventLoop\Factory::create();
    $socket = new React\Socket\Server($loop);
    $http = new React\Http\Server($socket, $loop);
    $st = new Storage('im.db');

    $http->on('request', function ($request, $response) use ($st) {
        $r = new Request($request, $response);
#       var_dump($r->request->getHeaders());
        if (!array_key_exists("Content-Length", $r->request->getHeaders())) {
            $r->abort(400, "need json content");
            return;
        }
        $r->contentLength = intval($r->request->getHeaders()["Content-Length"]);
        $request->on('data', function($data) use ($r, $st) {
            $r->addInput($data);
            if (!$r->readyToProcess())
                return;

            if (! ($js = $r->parseInput())) {
                $r->abort(400, "Invalid json");
                return;
            }

            global $methods;    // imported it from Methods.php.
                                // Failed to find how to import it explicitly
                                // (like python's "import Variable from Module")
            if (!isset($js->method) || !array_key_exists($js->method, $methods)) {
                $r->abort(400, "Invalid method");
                return;
            }
            $methods[$js->method]($r, $js, $st);
        });
    });

    echo "Server running at http://127.0.0.1:1337\n";

    $socket->listen(1337);
    $loop->run();
}

main();
?>
