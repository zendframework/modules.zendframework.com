<?php

namespace ZfModule\Controller\Exception;

use Application\Exception;
use Zend\Http;

class InvalidDataException extends Exception\ViewableUserException
{
    /**
     * Generates an Exception from a Invalid Post Request
     *
     * @param string $publicMessage
     * @param Http\Request $request
     * @return static
     */
    final public static function fromInvalidRequest($publicMessage, Http\Request $request)
    {
        return new static(
            sprintf('Invalid Request received [%s]', $request->toString()),
            (string) $publicMessage
        );
    }
}
