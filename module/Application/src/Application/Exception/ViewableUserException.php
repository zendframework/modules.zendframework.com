<?php

namespace Application\Exception;

class ViewableUserException extends \Exception implements ViewableUserExceptionInterface
{
    /* @var string */
    private $publicMessage;

    /**
     * {@inheritdoc}
     */
    public function __construct($message = '', $publicMessage = '', $code = 0, \Exception $previous = null)
    {
        $this->publicMessage = (string) $publicMessage;
        parent::__construct($message, $code, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicMessage()
    {
        return $this->publicMessage;
    }
}
