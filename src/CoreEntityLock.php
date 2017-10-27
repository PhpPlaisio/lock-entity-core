<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Abc\Lock;

use SetBased\Abc\Abc;
use SetBased\Exception\LogicException;

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
  public function acquireLock($nameId, $entityId)
  {
    $this->version  = Abc::$DL->abcLockEntityGetVersion(Abc::$companyResolver->getCmpId(), $nameId, $entityId);
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
  public function getEntityId()
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
  public function getName()
  {
    $this->ensureHoldLock();

    return Abc::$DL->abcLockEntityGetName($this->nameId);
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
  public function getNameId()
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
   *
   * @since 1.0.0
   * @api
   */
  public function updateVersion()
  {
    $this->ensureHoldLock();

    Abc::$DL->abcLockEntityUpdateVersion(Abc::$companyResolver->getCmpId(), $this->nameId, $this->entityId);
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
