<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\Lock;

use SetBased\Abc\Abc;
use SetBased\Exception\LogicException;

/**
 * Class for optimistically locking database entities.
 */
class CoreEntityLock
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The ID of the entity.
   *
   * @var int|null
   */
  private $entityId;

  /**
   * The ID of the type of the entity.
   *
   * @var int|null
   */
  private $typeId;

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
   * @param int $typeId   The ID of the type of the entity.
   * @param int $entityId The ID of the entity.
   *
   * @return void
   */
  public function acquireLock($typeId, $entityId)
  {
    $this->version  = Abc::$DL->abcLockEntityGetVersion(Abc::$companyResolver->getCmpId(), $typeId, $entityId);
    $this->typeId   = $typeId;
    $this->entityId = $entityId;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the ID of the locked entity.
   *
   * @return int
   */
  public function getEntityId()
  {
    $this->ensureHoldLock();

    return $this->entityId;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the ID of the type of the locked entity.
   *
   * @return int
   */
  public function getTypeId()
  {
    $this->ensureHoldLock();

    return $this->typeId;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the current version of the locked entity.
   *
   * @returns int
   */
  public function getVersion()
  {
    $this->ensureHoldLock();

    return $this->version;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Updates the version of the locked entity.
   *
   * @return void
   */
  public function updateVersion()
  {
    $this->ensureHoldLock();

    Abc::$DL->abcLockEntityUpdateVersion(Abc::$companyResolver->getCmpId(), $this->typeId, $this->entityId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Throws an exception if this object was never used to hold a lock.
   */
  private function ensureHoldLock()
  {
    if ($this->version===null)
    {
      throw new LogicException('No entity is locked');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
