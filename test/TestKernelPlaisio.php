<?php
declare(strict_types=1);

namespace Plaisio\Lock\Test;

use Plaisio\C;
use Plaisio\CompanyResolver\CompanyResolver;
use Plaisio\CompanyResolver\UniCompanyResolver;

/**
 * Kernel for testing purposes.
 */
class TestKernelPlaisio extends TestKernelSys
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the helper object for deriving the company.
   *
   * @return CompanyResolver
   */
  public function getCompanyResolver(): CompanyResolver
  {
    return new UniCompanyResolver(C::CMP_ID_PLAISIO);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
