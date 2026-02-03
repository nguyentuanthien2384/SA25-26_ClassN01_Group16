<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Load configuration
$config = require __DIR__ . '/config/config.php';

// Setup logger
$logger = new Logger($config['service']['name']);
$logger->pushHandler(new RotatingFileHandler(__DIR__ . '/logs/app.log', 7, Logger::DEBUG));
$logger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));

return [
    'config' => $config,
    'logger' => $logger,
];
