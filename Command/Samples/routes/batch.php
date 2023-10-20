<?php
echo '
<?php
$pathPrefix = defined("BASE_PATH") ? BASE_PATH : "";
require_once $pathPrefix . "framework/autoload_static.php";
require_once "'.$projectName.'/autoload_static.php";

/** components */
use framework\Batch\BatchScheduler;
use JoyPla\Batch\PayoutCorrection;
use JoyPla\Batch\ReservationPriceBatch;

$batchScheduler = new BatchScheduler();

// $batchScheduler->addJob((new HogeBatch())->everyMinute());

$batchScheduler->runJobs();
';