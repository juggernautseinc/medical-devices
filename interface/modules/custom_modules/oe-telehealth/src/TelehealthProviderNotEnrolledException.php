<?php

namespace Comlink\OpenEMR\Modules\TeleHealthModule;

use Throwable;

class TelehealthProviderNotEnrolledException extends \RuntimeException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
