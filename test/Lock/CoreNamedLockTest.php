<?php
declare(strict_types=1);

namespace SetBased\Abc\Test\Lock;

use PHPUnit\Framework\TestCase;
use SetBased\Abc\Abc;
use SetBased\Abc\C;
use SetBased\Abc\CompanyResolver\UniCompanyResolver;
use SetBased\Abc\Lock\CoreEntityLock;
use SetBased\Abc\Test\TestDataLayer;

/**
 * Test cases for Lock.
 */
class CoreNamedLockTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test locking twice (or more) the same entity is possible.
   */
  public function testDoubleLock(): void
  {
    $lock = new CoreEntityLock();

    $lock->acquireLock(C::LTN_ID_ENTITY_LOCK1, 123);
    $lock->acquireLock(C::LTN_ID_ENTITY_LOCK1, 123);
    $lock->acquireLock(C::LTN_ID_ENTITY_LOCK1, 123);

    self::assertTrue(true);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test get ID of the entity.
   */
  public function testEntityId1(): void
  {
    $lock = new CoreEntityLock();

    $lock->acquireLock(C::LTN_ID_ENTITY_LOCK1, 1235);
    $id = $lock->getEntityId();

    self::assertSame(1235, $id);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test get ID of entity lock without lock.
   */
  public function testEntityId2(): void
  {
    $lock = new CoreEntityLock();

    $this->expectException(\LogicException::class);
    $lock->getEntityId();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test lock is exclusive and released on commit.
   */
  public function testExclusiveLock1(): void
  {
    // Start helper process
    $descriptors = [0 => ["pipe", "r"],
                    1 => ["pipe", "w"]];

    $process = proc_open(__DIR__.'/../test-exclusive-lock-helper.php', $descriptors, $pipes);

    // Acquire lock.
    $lock = new CoreEntityLock();
    $lock->acquireLock(C::LTN_ID_ENTITY_LOCK1, 1234);

    // Tell helper process to acquire lock too.
    fwrite($pipes[0], "\n");

    // Do something.
    sleep(4);

    // Release lock.
    Abc::$DL->commit();

    // Read lock waiting time from child process.
    $time = fgets($pipes[1]);

    self::assertGreaterThanOrEqual(3, $time);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test lock is exclusive and released on rollback.
   */
  public function testExclusiveLock2(): void
  {
    // Start helper process
    $descriptors = [0 => ["pipe", "r"],
                    1 => ["pipe", "w"]];

    $process = proc_open(__DIR__.'/../test-exclusive-lock-helper.php', $descriptors, $pipes);

    // Acquire lock.
    $lock = new CoreEntityLock();
    $lock->acquireLock(C::LTN_ID_ENTITY_LOCK1, 1234);

    // Tell helper process to acquire lock too.
    fwrite($pipes[0], "\n");

    // Do something.
    sleep(4);

    // Release lock.
    Abc::$DL->rollback();

    // Read lock waiting time from child process.
    $time = fgets($pipes[1]);

    self::assertGreaterThanOrEqual(3, $time);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test locks are company isolated.
   */
  public function testExclusiveLock3(): void
  {
    Abc::$companyResolver = new UniCompanyResolver(C::CMP_ID_SYS);

    // Start helper process
    $descriptors = [0 => ["pipe", "r"],
                    1 => ["pipe", "w"]];

    $process = proc_open(__DIR__.'/../test-exclusive-lock-helper.php', $descriptors, $pipes);

    // Acquire lock.
    $lock = new CoreEntityLock();
    $lock->acquireLock(C::LTN_ID_ENTITY_LOCK1, 1234);

    // Tell helper process to acquire lock too.
    fwrite($pipes[0], "\n");

    // Do something.
    sleep(4);

    // Release lock.
    Abc::$DL->commit();

    // Read lock waiting time from child process.
    $time = fgets($pipes[1]);

    self::assertEquals(0, $time);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test get ID of the type of the locked entity.
   */
  public function testGetId1(): void
  {
    $lock = new CoreEntityLock();

    $lock->acquireLock(C::LTN_ID_ENTITY_LOCK1, 1235);
    $id = $lock->getNameId();

    self::assertSame(C::LTN_ID_ENTITY_LOCK1, $id);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test get ID of the type of the locked entity. without lock.
   */
  public function testGetId2(): void
  {
    $lock = new CoreEntityLock();

    $this->expectException(\LogicException::class);
    $lock->getNameId();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test get name of entity lock.
   */
  public function testGetName1(): void
  {
    $lock = new CoreEntityLock();

    $lock->acquireLock(C::LTN_ID_ENTITY_LOCK1, 1238);
    $name = $lock->getName();

    self::assertSame('named_lock1', $name);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test get name of entity lock without lock.
   */
  public function testGetName2(): void
  {
    $lock = new CoreEntityLock();

    $this->expectException(\LogicException::class);
    $lock->getName();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test multiple entity locks on the entity are possible.
   */
  public function testMultipleLocks(): void
  {
    $lock1 = new CoreEntityLock();
    $lock1->acquireLock(C::LTN_ID_ENTITY_LOCK1, 1236);

    $lock2 = new CoreEntityLock();
    $lock2->acquireLock(C::LTN_ID_ENTITY_LOCK2, 1236);

    self::assertTrue(true);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test locking versions.
   */
  public function testVersion(): void
  {
    $lock1 = new CoreEntityLock();
    $lock1->acquireLock(C::LTN_ID_ENTITY_LOCK1, 1237);
    $version1 = $lock1->getVersion();
    $lock1->updateVersion();

    self::assertIsInt($version1);

    $lock2 = new CoreEntityLock();
    $lock2->acquireLock(C::LTN_ID_ENTITY_LOCK1, 1237);
    $version2 = $lock2->getVersion();
    self::assertNotEquals($version1, $version2);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to the MySQL server and cleans the BLOB tables.
   */
  protected function setUp(): void
  {
    Abc::$DL              = new TestDataLayer();
    Abc::$companyResolver = new UniCompanyResolver(C::CMP_ID_ABC);

    Abc::$DL->connect('localhost', 'test', 'test', 'test');
    Abc::$DL->begin();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Disconnects from the MySQL server.
   */
  protected function tearDown(): void
  {
    Abc::$DL->commit();
    Abc::$DL->disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
