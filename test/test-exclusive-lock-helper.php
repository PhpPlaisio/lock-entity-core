<?php
declare(strict_types=1);

use Plaisio\C;
use Plaisio\CompanyResolver\UniCompanyResolver;
use Plaisio\Kernel\Nub;
use Plaisio\Lock\CoreEntityLock;
use Plaisio\Lock\Test\TestDataLayer;

require __DIR__.'/../vendor/autoload.php';

// Setup ABC.
Nub::$DL              = new TestDataLayer();
Nub::$companyResolver = new UniCompanyResolver(C::CMP_ID_ABC);
Nub::$DL->connect('localhost', 'test', 'test', 'test');
Nub::$DL->begin();

// Start time.
$time0 = time();

// Wait for parent process.
$handle = fopen('php://stdin', 'rt');
$read = fgets($handle);

// Acquire lock.
$lock = new CoreEntityLock();
$lock->acquireLock(C::LTN_ID_ENTITY_LOCK1, 1234);

// End time.
$time1 = time();

echo $time1 - $time0;
