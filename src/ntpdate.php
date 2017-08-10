<?php

    require('packet.php');

    // Unsure there is a URL parameter
    $argc == 2 or die('USAGE: <URL>');
    $url = $argv[1];
    $port = 123;

    send($url, $port);

    function send($url, $port) {

        $version = 4;
        $mode = 3;

        $packet = new Packet(48);
        $packet->write(2, 5, $version);
        $packet->write(5, 8, $mode);
        $packet->debug(4);

        $data = $packet->data();
        $datalen = $packet->datalen();

        // echo $packet->representation() . "\n";

        // $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        // $res = socket_sendto($sock, $data, $datalen, 0x100, $url, $port);

        // return $res !== FALSE;
    }
?>
