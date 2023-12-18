<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class WebSocketHandler implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->broadcastAdminStatus(true); // Notify clients that admin is online
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Handle messages if needed
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $this->broadcastAdminStatus(false); // Notify clients that admin is offline
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }

    protected function broadcastAdminStatus($online)
    {
        foreach ($this->clients as $client) {
            $client->send(json_encode(['adminOnline' => $online]));
        }
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocketHandler()
        )
    ),
    8080
);

$server->run();
