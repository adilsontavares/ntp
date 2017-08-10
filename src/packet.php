<?php

    require_once("ansi-color.php");
    use PhpAnsiColor\Color;

    class Packet {

        protected $str = '';

        private function __construct() {}

        public static function withLength($length) {
            $packet = new Packet();
            $packet->str = str_repeat('0', $length * 8);
            return $packet;
        }

        public static function withData($data, $datalen) {
            
            $str = '';
            for ($i = 0; $i < $datalen; $i++) {
                $binstr = decbin(ord($data[$i]));
                $str = $str . str_pad($binstr, 8, '0', STR_PAD_LEFT);
            }
            
            $packet = new Packet();
            $packet->str = $str;
            return $packet;
        }

        public function write($start, $end, $value) {
            
            $length = $end - $start;
            $valstr = str_pad(decbin($value), $length, '0', STR_PAD_LEFT);
            $valstr = substr($valstr, 0, $length);

            for ($i = $start; $i < $end; $i++) {
                $this->str[$i] = $valstr[$i - $start];
            }
        }

        public function data() {
            
            $datalen = $this->datalen();
            $data = str_repeat(0, $datalen);

            for ($i = 0; $i < $datalen; $i++) {
                $binstr = substr($this->str, $i * 8, 8);
                $num = bindec($binstr);
                $data[$i] = chr($num);
            }

            return $data;
        }

        public function representation() {
            return $this->str;
        }

        public function debug($bytesPerLine = 4) {

            $data = $this->data();
            $datalen = $this->datalen();
            $headerColW = 5;

            echo "\n";
            echo str_repeat(' ', $headerColW + 1) . ' ';
            for ($i = 0; $i < $bytesPerLine; $i++){
                echo str_pad($i * 8, 9);
            }
            echo "\n";

            echo str_repeat(' ', $headerColW + 1);
            for ($i = 0; $i < $bytesPerLine; $i++) echo '+--------';
            echo "\n";

            echo str_pad(0, $headerColW, ' ', STR_PAD_LEFT) . ' ';
            for ($i = 0; $i < $datalen; $i++) {

                echo '|';
                $binstr = str_pad(decbin(ord($data[$i])), 8, '0', STR_PAD_LEFT);

                for ($k = 0; $k < strlen($binstr); $k++) {
                    if ($binstr[$k] == '1') {
                        echo Color::set('1', 'blue');
                    } else {
                        echo '0';
                    }
                }
                
                if (($i + 1) % $bytesPerLine == 0) {
                    
                    printf("\n");

                    if ($i == 47) {
                        echo str_repeat(' ', $headerColW) . ' ';
                    } else {
                        echo str_pad(($i + 1) * 8, $headerColW, ' ', STR_PAD_LEFT) . ' ';
                    }
                }
            }

            echo "\n";
        }

        public function datalen() {
            return strlen($this->str) / 8;
        }
    }
?>