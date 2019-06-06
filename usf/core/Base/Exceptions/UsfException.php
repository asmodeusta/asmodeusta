<?php

namespace Usf\Base\Exceptions;

use Throwable;

/**
 * Class UsfException
 * @package Usf\Core\Base\Exceptions
 */
class UsfException extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return 'Code #' . $this->code . ': ' . parent::__toString();
    }

}