<?php
declare(strict_types=1);

namespace Plaisio\Lock;

use Plaisio\PlaisioObject;

/**
 * Factory for creating locks on database entities.
 */
class CoreEntityLockFactory extends PlaisioObject implements EntityLockFactory
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function create(int $nameId, int $entityId): EntityLock
  {
    $lock = new CoreEntityLock($this);
    $lock->acquireLock($nameId, $entityId);

    return $lock;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
