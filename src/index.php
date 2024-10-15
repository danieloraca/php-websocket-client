<?php

require './vendor/autoload.php';

use Ratchet\Client\Connector;
use React\EventLoop\Loop;
use React\Socket\Connector as ReactConnector;

$wsUrl = 'ws://localhost:8000/ws';

// Payload to send
$payload = json_encode([
    "type" => "response.create",
    "response" => [
        "modalities" => ["audio", "text"],
        "instructions" => "What is 1+1?"
    ]
]);

// Get the event loop instance
$loop = Loop::get();
$connector = new Connector($loop, new ReactConnector($loop));

// Connect to the WebSocket server
$connector($wsUrl)->then(function($conn) use ($payload) {
    echo "Connected to WebSocket!\n";
    // Send the payload
    $conn->send($payload);
    echo "Payload sent: " . $payload . "\n";

    // Listen for incoming messages
    $conn->on('message', function($msg) {
        echo "Received message: $msg\n";
    });

    // Handle closing of the connection
    $conn->on('close', function($code = null, $reason = null) {
        echo "Connection closed (Code: $code, Reason: $reason)\n";
    });

}, function(\Exception $e) use ($loop) {
    echo "Could not connect: {$e->getMessage()}\n";
    $loop->stop();
});

// Run the event loop
$loop->run();

