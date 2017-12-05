<?php
/**
 */

require dirname(__DIR__) . '/bootstrap.php';
use Remind\Api\Base\ApiRouter;

$instance = new ApiRouter();
$instance->dispatch();