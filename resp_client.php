<?php
namespace Uberlog;

class RespClient
{
    private $__sock;

    function __construct($host, $port, $timeout = null) {
        $timeout = $timeout ?: ini_get("default_socket_timeout");
        $this->__sock = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($this->__sock === FALSE) {
            throw new \Exception("{$errno} - {$errstr}");
        }
    }

    function __destruct() {
        fclose($this->__sock);
    }

    function __call($name, $args) {
        /* Build the Redis unified protocol command */
        $crlf = "\r\n";
        array_unshift($args, strtoupper($name));
        $command = '*' . count($args) . $crlf;
        foreach ($args as $arg) {
            $command .= '$' . strlen($arg) . $crlf . $arg . $crlf;
        }

        for ($written = 0; $written < strlen($command); $written += $fwrite) {
            $fwrite = fwrite($this->__sock, substr($command, $written));
            if ($fwrite === FALSE || $fwrite <= 0) {
                throw new \Exception('Failed to write entire command to stream');
            }
        }
        return $this->readResponse();
    }

    private function readResponse() {
        $reply = trim(fgets($this->__sock, 512));
        switch (substr($reply, 0, 1)) {
        case '-':
            throw new \Exception(trim(substr($reply, 4)));
            break;
        case '+':
            $response = substr(trim($reply), 1);
            if ($response === 'OK') {
                $response = TRUE;
            }
            break;
        case '$':
            $response = NULL;
            if ($reply == '$-1') {
                break;
            }
            $read = 0;
            $size = intval(substr($reply, 1));
            if ($size > 0) {
                do {
                    $block_size = ($size - $read) > 1024 ? 1024 : ($size - $read);
                    $r = fread($this->__sock, $block_size);
                    if ($r === FALSE) {
                        throw new \Exception('Failed to read response from stream');
                    } else {
                        $read += strlen($r);
                        $response .= $r;
                    }
                } while ($read < $size);
            }
            fread($this->__sock, 2); /* discard crlf */
            break;
        case '*':
            $count = intval(substr($reply, 1));
            if ($count == '-1') {
                return NULL;
            }
            $response = array();
            for ($i = 0; $i < $count; $i++) {
                $response[] = $this->readResponse();
            }
            break;
        case ':':
            $response = intval(substr(trim($reply), 1));
            break;
        default:
            throw new RedisException("Unknown response: {$reply}");
            break;
        }
        return $response;
    }

}
