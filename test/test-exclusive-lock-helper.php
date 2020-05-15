<?php
declare(strict_types=1);

use Plaisio\C;
use Plaisio\Lock\CoreEntityLock;
use Plaisio\Lock\Test\TestKernelPlaisio;

require __DIR__.'/../vendor/autoload.php';

// Setup kernel.
$kernel = new TestKernelPlaisio();

// Start time.
$time0 = time();

// Wait for parent process.
$handle = fopen('php://stdin', 'rt');
$read   = fgets($handle);

// Acquire lock.
$lock = new CoreEntityLock($kernel);
$lock->acquireLock(C::LTN_ID_ENTITY_LOCK1, 1234);

// End time.
$time1 = time();

echo $time1 - $time0;
