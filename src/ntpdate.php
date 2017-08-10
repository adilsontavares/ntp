<?php

    require('packet.php');

    // Unsure there is a URL parameter
    $argc == 2 or die('USAGE: <URL>');
    $url = $argv[1];
    $port = 123;

    send($sock, $url, $port);
    receive($sock, $url, $port);

    echo "T1 = $t1\n";
    echo "T2 = $t2\n";
    echo "T3 = $t3\n";
    echo "T4 = $t4\n";

    echo "\n";

    echo "Local:    " . date("d/M/y H:i:s", $t4) . "\n";
    echo "Servidor: " . date("d/M/y H:i:s", $t3) . "\n";

    function send(&$sock, $url, $port) {

        global $t1;

        $version = 4;
        $mode = 3;

        $packet = Packet::withLength(48);
        $packet->write(2, 5, $version);
        $packet->write(5, 8, $mode);
        $packet->debug();

        $data = $packet->data();

        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_connect($sock, $url, $port) or die('Could not connect to host.');

        $t1 = time();
        socket_send($sock, $data, strlen($data), 0);
    }

    function receive(&$sock, $url, $port) {

        global $t2, $t3, $t4;

        socket_recv($sock, $data, 48, MSG_WAITALL);
        $t4 = time();

        socket_close($sock);
        
        $packet = Packet::withData($data, 48);
        $packet->debug();

        $res = unpack("N12", $packet->data());
        $t2 = (double)$res[9] + 1.0 / (double)$res[10];
        $t3 = (double)$res[11] + 1.0 / (double)$res[12];
    }
?>
