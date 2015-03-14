<?php

namespace Application\Exception;

interface ViewableUserExceptionInterface
{
    /**
     * @param string     $message   Error Message for internal logging
     * @param string     $publicMessage Public Error Message
     * @param int        $code  Error Code
     * @param \Exception $previous Previous Exception
     */
    public function __construct($message = '', $publicMessage = '', $code = 0, \Exception $previous = null);

    /**
     * Returns the Public Error Message
     * @return string
     */
    public function getPublicMessage();
}
