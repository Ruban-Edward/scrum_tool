<?php

namespace App\Websockets;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class PockerServer implements MessageComponentInterface {
    protected $clients;
    private $votes = [];
    private $usernames = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        $action = $data['action'] ?? null;
        $username = $data['user'] ?? null;

        switch ($action) {
            case 'reveal':
                $this->handleReveal();
                break;
            case 'restart':
                $this->handleRestart();
                break;
            case 'updateVotes':
                $this->updateVotes($username, $data['value']);
                break;
            case 'exit':
                $this->handleExit($from, $username);
                break;
            case 'join':
                $this->handleJoin($from, $username);
                break;
        }
    }

    public function updateVotes($user, $value) {
        $this->votes[$user] = $value;
        $this->broadcastVoteCount();
    }

    private function handleReveal() {
        $minVote = min($this->votes);
        $maxVote = max($this->votes);
        $minUsers = array_keys($this->votes, $minVote);
        $maxUsers = array_keys($this->votes, $maxVote);

        $this->broadcastMessage([
            'action' => 'reveal',
            'votes' => $this->votes,
            'minVote' => $minVote,
            'maxVote' => $maxVote,
            'minUsers' => $minUsers,
            'maxUsers' => $maxUsers
        ]);
    }

    private function handleExit(ConnectionInterface $from, $username) {
        unset($this->votes[$username]);
        $this->clients->detach($from);
        $this->broadcastMessage([
            'action' => 'exit',
            'user' => $username,
            'message' => "$username has left the game"
        ]);
        $this->updateOnlineUsers();
    }

    private function handleJoin(ConnectionInterface $from, $username) {
        $this->usernames[$from->resourceId] = $username;
        $this->broadcastMessage([
            'action' => 'join',
            'user' => $username,
            'message' => "$username has joined the game"
        ]);
        $this->updateOnlineUsers();
    }

    private function broadcastVoteCount() {
        $this->broadcastMessage([
            'action' => 'updateVoteCount',
            'numberofvotes' => count($this->votes)
        ]);
    }

    private function updateOnlineUsers() {
        $onlineUsers = array_values($this->usernames);
        $this->broadcastMessage([
            'action' => 'updateOnlineUsers',
            'onlineUsers' => $onlineUsers
        ]);
    }

    private function handleRestart() {
        $this->votes = [];
        $this->broadcastMessage(['action' => 'restart']);
    }

    private function broadcastMessage($message) {
        foreach ($this->clients as $client) {
            $client->send(json_encode($message));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $username = $this->usernames[$conn->resourceId] ?? null;

        unset($this->usernames[$conn->resourceId]);
        $this->clients->detach($conn);

        if ($username) {
            unset($this->votes[$username]);
            $this->broadcastMessage([
                'action' => 'userDisconnected',
                'user' => $username,
                'message' => "$username has disconnected"
            ]);
            $this->updateOnlineUsers();
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}
