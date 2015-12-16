#!/usr/bin/env php
<?php
require 'vendor/autoload.php';
require 'util.php';
require 'Methods.php';

class Request {
    public $request;
    public $response;
    public $contentLength;

    private $rawInputJson;
    function __construct($rq, $rp) {
        echo "req init\n";
        $this->request = $rq;
        $this->response = $rp;
    }

    function __destruct() {
        echo "req done\n";
    }

    function abort($errcode, $reason) {
        $this->sendAndClose($errcode, $reason);
    }

    function done($data) {
        $this->sendAndClose(200, $data);
    }

    function addInput($data) {
        $this->rawInputJson .= $data;
    }

    function readyToProcess() {
        return strlen($this->rawInputJson) >= $this->contentLength;
    }

    function parseInput() {
        $inputJson = json_decode($this->rawInputJson);
        if ($inputJson === NULL) {
        }
        return $inputJson;
    }

    private function sendAndClose($errcode, $data) {
        $this->response->writeHead($errcode, array('Content-Type' => 'text/plain'));
        $this->response->end("$data\n");
    }
}

function main() {
    $loop = React\EventLoop\Factory::create();
    $socket = new React\Socket\Server($loop);
    $http = new React\Http\Server($socket, $loop);

    $http->on('request', function ($request, $response) {
        $r = new Request($request, $response);
#       var_dump($r->request->getHeaders());
        if (!array_key_exists("Content-Length", $r->request->getHeaders())) {
            $r->abort(400, "need json content");
            return;
        }
        $r->contentLength = intval($r->request->getHeaders()["Content-Length"]);
        $request->on('data', function($data) use ($r) {
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
            $methods[$js->method]($r);
        });
    });

    echo "Server running at http://127.0.0.1:1337\n";

    $socket->listen(1337);
    $loop->run();
}

main();
?>
