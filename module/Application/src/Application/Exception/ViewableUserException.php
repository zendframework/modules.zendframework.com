<?php

namespace Application\Exception;

class ViewableUserException extends \Exception
{
    protected $publicMessage;

    public function __construct($message = '', $publicMessage = '', $code = 0, \Exception $previous = null)
    {
        $this->publicMessage = (string) $publicMessage;
        parent::__construct($message, $code, $previous);
    }

    final public function getPublicMessage()
    {
        return $this->publicMessage;
    }
}
