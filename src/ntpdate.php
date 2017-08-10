<?php

    require('packet.php');

    // Unsure there is a HOST parameter
    $argc == 2 or die('USAGE: <HOST>');
    $host = $argv[1];
    $port = 123;

    $request = create_request();
    send($request);

    $response = receive($sock, $host, $port);
    interpret($response);

    // print_debug();
    print_ntp();

    function create_request() {

        $version = 4;
        $mode = 3;

        $packet = Packet::withLength(48);
        $packet->write(2, 5, $version);
        $packet->write(5, 8, $mode);

        return $packet;
    }

    function send($packet) {

        global $sock, $host, $port, $t1;

        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_connect($sock, $host, $port) or die('Could not connect to host.');

        $data = $packet->data();
        $t1 = microtime(true);

        socket_send($sock, $data, strlen($data), 0);
    }

    function receive() {

        global $sock, $t4;

        socket_recv($sock, $data, 48, MSG_WAITALL);
        $t4 = microtime(true);

        socket_close($sock);
        return Packet::withData($data, 48);
    }

    function interpret($packet) {

        global $t1, $t2, $t3, $t4, $stratum, $offset, $delay;

        $res = unpack("N12", $packet->data());

        $t2 = timestamp_to_sec($res[9], $res[10]);
        $t3 = timestamp_to_sec($res[11], $res[12]);

        $stratum = unpack("C2", $packet->data())[2];

        $offset = (($t2 - $t1) + ($t3 - $t4)) * 0.5;
        $delay = (($t4 - $t1) - ($t3 - $t2)) * 0.5;
    }

    function timestamp_to_sec($seconds, $fraction) {
        
        $offset = 2208988800.0;
        $micro = ($fraction * 1000000.0) / pow(2.0, 32.0);
        $frac = $micro / 1000000.0;

        return ($seconds + $frac) - $offset;
    }

    function print_debug() {

        global $t1, $t2, $t3, $t4, $offset, $delay, $stratum, $host, $request, $response;

        echo "\n";
        echo "PACKET REQUEST:\n";
        $request->debug();

        echo "PACKET RESPONSE:\n";
        $response->debug();

        echo "DATA:\n\n";

        echo "T1: $t1\n";
        echo "T2: $t2\n";
        echo "T3: $t3\n";
        echo "T4: $t4\n";
        echo "\n";

        echo "Offset:  " . sprintf("%.3f", $offset * 1000.0) . "ms\n";
        echo "Delay:   " . sprintf("%.3f", $delay * 1000.0) . "ms\n";
        echo "Stratum: $stratum\n";
        echo "\n";

        echo "Local:   " . date("d/M/y H:i:s", $t4) . "\n";
        echo "Server:  " . date("d/M/y H:i:s", $t3) . "\n";
        echo "\n";
    }

    function print_ntp() {

        global $host, $stratum, $offset, $delay;

        echo "server $host, "; 
        echo "stratum $stratum, ";
        echo "offset " . sprintf("%.3f", $offset * 1000.0) . "ms, ";
        echo "delay ".   sprintf("%.3f", $delay * 1000.0) ."ms\n";
    }
?>
