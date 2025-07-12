<?php

namespace Cinebox\App\Helpers;

use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\WebProcessor;
use Monolog\Handler\FilterHandler;
use Monolog\Formatter\JsonFormatter;

date_default_timezone_set('America/Sao_Paulo');

class Log
{
    private static array $loggers = [];

    public static function getLogger(string $type): Logger
    {
        if (!isset(self::$loggers[$type])) {
            $logger = new Logger('cinebox');

            $logDir = __DIR__ . '/../../logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }

            $typeDir = $logDir . "/$type";
            if (!is_dir($typeDir)) {
                mkdir($typeDir, 0777, true);
            }

            $fileName = "$type-" . date('Y-m-d');

            $formatter = new JsonFormatter();

            $streamHandler = match ($type) {
                'info' => new StreamHandler($typeDir . "/$fileName.log", Level::Info),
                'warning' => new StreamHandler($typeDir . "/$fileName.log", Level::Warning),
                'error' => new StreamHandler($typeDir . "/$fileName.log", Level::Error)
            };

            $streamHandler->setFormatter($formatter);

            $filterHandler = match ($type) {
                'info' => new FilterHandler($streamHandler, Level::Info, Level::Info),
                'warning' => new FilterHandler($streamHandler, Level::Warning, Level::Warning),
                'error' => new FilterHandler($streamHandler, Level::Error, Level::Emergency)
            };

            $logger->pushHandler($filterHandler);

            $logger->pushProcessor(new WebProcessor());

            self::$loggers[$type] = $logger;
        }

        return self::$loggers[$type];
    }

    public static function info(string $message, array $context = []): void
    {
        self::getLogger('info')->info($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::getLogger('warning')->warning($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::getLogger('error')->error($message, $context);
    }
}
