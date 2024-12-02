<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;

class StartWebSocketServer extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'websocket:start';
    protected $description = 'Starts the WebSocket server.';

    public function run(array $params = [])
    {
        $server = new WebSocketServer();
        $server->start();
    }
}
