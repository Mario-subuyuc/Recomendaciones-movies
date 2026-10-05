<?php

namespace App\Exceptions;

use RuntimeException;

class ErrorChat extends RuntimeException
{
    public function __construct(string $message, public readonly int $estado = 503)
    {
        parent::__construct($message);
    }
}
