<?php

    require('packet.php');

    // Unsure there is a URL parameter
    $argc == 2 or die('USAGE: <URL>');
    $url = $argv[1];
    $port = 123;

    send($sock, $url, $port);
    receive($sock, $url, $port);

    function send(&$sock, $url, $port) {

        $version = 4;
        $mode = 3;

        $packet = Packet::withLength(48);
        $packet->write(2, 5, $version);
        $packet->write(5, 8, $mode);
        $packet->debug();

        $data = $packet->data();

        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_connect($sock, $url, $port) or die('Could not connect to host.');
        socket_send($sock, $data, strlen($data), 0);
    }

    function receive(&$sock, $url, $port) {

        socket_recv($sock, $data, 48, MSG_WAITALL);
        socket_close($sock);
        $packet = Packet::withData($data, 48);
        $packet->debug();
    }
?>
