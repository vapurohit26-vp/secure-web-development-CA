<?php


function security_log($message) {
    $logFile = __DIR__ . '/logs/security.log';

    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0700, true);
    }

    $time = date('Y-m-d H:i:s');
    $line = "[$time] $message" . PHP_EOL;

    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}
