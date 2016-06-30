<?php

namespace Meister\Meister\Libraries;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log {

    /**
     * @param $desc
     */
    public static function warning($desc) {
        $log = new Logger('Project');

        $log->pushHandler(new StreamHandler(__DIR__."/../log/register.log", Logger::WARNING));
        $log->addWarning($desc);
    }

    /**
     * @param $desc
     */
    public static function error($desc) {
        $log = new Logger('Project');

        $log->pushHandler(new StreamHandler(__DIR__."/../log/register.log", Logger::ERROR));
        $log->addError($desc);
    }

    /**
     * @param $desc
     */
    public static function critical($desc) {
        $log = new Logger('Project');

        $log->pushHandler(new StreamHandler(__DIR__."/../log/register.log", Logger::CRITICAL));
        $log->addCritical($desc);
    }

    /**
     * @param $desc
     */
    public static function info($desc) {
        $log = new Logger('Project');

        $log->pushHandler(new StreamHandler(__DIR__."/../log/register.log", Logger::INFO));
        $log->addInfo($desc);
    }

    /**
     * @param $desc
     */
    public static function notice($desc) {
        $log = new Logger('Project');

        $log->pushHandler(new StreamHandler(__DIR__."/../log/register.log", Logger::NOTICE));
        $log->addNotice($desc);
    }

    /**
     * @param $desc
     * @param $file
     */
    public static function app($desc,$file) {
        $log = new Logger('Project');

        $log->pushHandler(new StreamHandler(__DIR__."/../log/{$file}.log", Logger::INFO));
        $log->addNotice(json_encode($desc));
    }
}