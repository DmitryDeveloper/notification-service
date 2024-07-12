<?php

namespace App\Exceptions;

use Exception;

class TemplateIsNotSetException extends Exception
{
    public function __construct()
    {
        parent::__construct('Template is not set');
    }
}
