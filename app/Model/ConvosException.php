<?php
namespace App\Model;

use Exception;
use Illuminate\Contracts\Support\MessageBag;

/**
 * Custom conversations service exception
 *
 * @package App\Model
 */
class ConvosException extends Exception
{
    /**
     * @param MessageBag $validationError
     */
    public function __construct(MessageBag $validationError)
    {
        $this->validationError = $validationError;
    }

    /**
     * @return MessageBag
     */
    public function getValidationError()
    {
        return $this->validationError;
    }
}