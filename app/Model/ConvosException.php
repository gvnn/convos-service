<?php
namespace App\Model;

use Exception;
use Illuminate\Contracts\Support\MessageBag;

class ConvosException extends Exception
{
    public function __construct(MessageBag $validationError)
    {
        $this->validationError = $validationError;
    }

    public function getValidationError()
    {
        return $this->validationError;
    }
}