<?php

namespace App\Exceptions;

use Exception;

class OfficeAlreadyAssignedException extends Exception
{
    public function __construct()
    {
        parent::__construct("The realtor has already been assigned to this office!");
    }
}
