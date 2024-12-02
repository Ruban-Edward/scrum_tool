<?php

namespace App\Commands;
use CodeIgniter\CLI\CLI;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Websockets\PockerServer;

class WebSocketServer
{
    // protected $port = 8081;
    // public $host = 'localhost';
    public function start()
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new PockerServer()
                )
            ),
            // $this->checkPort($this->host, $this->port)
            8083
        );


        $server->run();
    }

    // public function checkPort($host, $port)
    // {
    //     $connection = @fsockopen($host, $port);
    //     if (is_resource($connection)) {
    //         fclose($connection);
    //         CLI::write("Port $port is available.", 'green');
    //         return $port;
    //     } else {
    //         return $this->checkPort($host, $port + 1);
    //     }
    // }

}
