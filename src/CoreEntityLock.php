<?php
declare(strict_types=1);

namespace Plaisio\Lock;

use Plaisio\PlaisioObject;

/**
 * Class for optimistically locking database entities.
 */
class CoreEntityLock extends PlaisioObject implements EntityLock
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The ID of the entity.
   *
   * @var int|null
   */
  private ?int $entityId = null;

  /**
   * The ID of the name of the entity lock.
   *
   * @var int|null
   */
  private ?int $nameId;

  /**
   * The current version of the entity.
   *
   * @var int|null
   */
  private ?int $version = null;

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
    $this->nameId   = $nameId;
    $this->entityId = $entityId;
    $this->version  = $this->nub->DL->abcLockEntityCoreGetVersion($this->nub->company->cmpId, $nameId, $entityId);
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

    return $this->nub->DL->abcLockEntityCoreGetName($this->nameId);
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

    $this->nub->DL->abcLockEntityCoreUpdateVersion($this->nub->company->cmpId, $this->nameId, $this->entityId);
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
