<?php

    require_once("ansi-color.php");
    use PhpAnsiColor\Color;

    class Packet {

        protected $str = '';

        public function __construct($length) {
            $this->str = str_repeat('0', $length * 8);
        }

        public function write($start, $end, $value) {
            
            $length = $end - $start;
            $valstr = sprintf("%0" . $length . "d", decbin($value));
            $valstr = substr($valstr, 0, $length);

            for ($i = $start; $i < $end; $i++) {
                $this->str[$i] = $valstr[$i - $start];
            }
        }

        public function data() {
            
            for ($i = 0; $i < strlen($this->str); $i++)
                $this->str[$i] = rand() % 2 == 0 ? '0' : 1;

            $datalen = $this->datalen();
            $data = array_fill(0, $datalen, 0);

            for ($i = 0; $i < $datalen; $i++) {

                $binstr = substr($this->str, $i * 8, 8);
                $data[$i] = bindec($binstr);
            }

            return $data;
        }

        public function representation() {
            return $this->str;
        }

        public function debug($bytesPerLine) {

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
                $binstr = sprintf("%08d", decbin($data[$i]));

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