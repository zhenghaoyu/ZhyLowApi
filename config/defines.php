<?php

$dir  = dirname(dirname(__DIR__));
$rootDir = dirname($dir);
if (!defined('REMIND_API')) {
    define('REMIND_API', $dir . '/zhy_api');
}

