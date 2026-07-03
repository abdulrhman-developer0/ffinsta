<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientPointsException extends RuntimeException
{
    public function __construct(int $required, int $available)
    {
        parent::__construct(
            "Insufficient points. Required: {$required}, Available: {$available}."
        );
    }
}
