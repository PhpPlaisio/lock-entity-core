<?php
declare(strict_types=1);

namespace Plaisio\Lock;

use Plaisio\Kernel\Nub;

/**
 * Class for optimistically locking database entities.
 */
class CoreEntityLock implements EntityLock
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The ID of the entity.
   *
   * @var int|null
   */
  private $entityId;

  /**
   * The ID of the name of the entity lock.
   *
   * @var int|null
   */
  private $nameId;

  /**
   * The current version of the entity.
   *
   * @var int
   */
  private $version;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Acquires a lock on an object.
   *
   * @param int $nameId   The ID of the name of the entity lock.
   * @param int $entityId The ID of the entity.
   *
   * @return void
   *
   * @since 1.0.0
   * @api
   */
  public function acquireLock(int $nameId, int $entityId): void
  {
    $this->version  = Nub::$DL->abcLockEntityCoreGetVersion(Nub::$companyResolver->getCmpId(), $nameId, $entityId);
    $this->nameId   = $nameId;
    $this->entityId = $entityId;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the ID of the locked entity.
   *
   * @return int
   *
   * @since 1.0.0
   * @api
   */
  public function getEntityId(): int
  {
    $this->ensureHoldLock();

    return $this->entityId;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the name of the entity lock.
   *
   * @return string
   *
   * @since 1.0.0
   * @api
   */
  public function getName(): string
  {
    $this->ensureHoldLock();

    return Nub::$DL->abcLockEntityCoreGetName($this->nameId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the ID of the name of the entity lock.
   *
   * @return int
   *
   * @since 1.0.0
   * @api
   */
  public function getNameId(): int
  {
    $this->ensureHoldLock();

    return $this->nameId;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the current version of the locked entity.
   *
   * @returns int
   *
   * @since 1.0.0
   * @api
   */
  public function getVersion(): int
  {
    $this->ensureHoldLock();

    return $this->version;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Updates the version of the locked entity.
   *
   * @return void
   *
   * @since 1.0.0
   * @api
   */
  public function updateVersion(): void
  {
    $this->ensureHoldLock();

    Nub::$DL->abcLockEntityCoreUpdateVersion(Nub::$companyResolver->getCmpId(), $this->nameId, $this->entityId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Throws an exception if this object was never used to hold a lock.
   */
  private function ensureHoldLock(): void
  {
    if ($this->version===null)
    {
      throw new \LogicException('No entity is locked');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
