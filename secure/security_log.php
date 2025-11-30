<?php


function security_log($message) {
    $logFile = __DIR__ . '/logs/security.log';

    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0700, true);
    }

    $ip   = $_SERVER['REMOTE_ADDR']    ?? 'unknown_ip';
    $ua   = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown_ua';
    $time = date('Y-m-d H:i:s');

    $line = "[$time] [IP: $ip] [UA: $ua] $message" . PHP_EOL;

    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}
