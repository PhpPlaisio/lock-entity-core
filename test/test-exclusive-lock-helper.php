#!/usr/bin/env php
<?php
//----------------------------------------------------------------------------------------------------------------------
use SetBased\Abc\Abc;
use SetBased\Abc\C;
use SetBased\Abc\CompanyResolver\UniCompanyResolver;
use SetBased\Abc\Lock\CoreEntityLock;
use SetBased\Abc\Test\TestDataLayer;

require __DIR__.'/../vendor/autoload.php';

//----------------------------------------------------------------------------------------------------------------------
// Setup ABC.
Abc::$DL              = new TestDataLayer();
Abc::$companyResolver = new UniCompanyResolver(C::CMP_ID_ABC);
Abc::$DL->connect('localhost', 'test', 'test', 'test');
Abc::$DL->begin();

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
