<?php

namespace Application\Service;

use Exception;
use Psr\Log\LoggerInterface;

class ErrorHandlingService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function logException(Exception $exception)
    {
        $this->logger->error(
            $exception->getMessage(),
            [
                'previous' => $this->previousExceptionMessages($exception),
                'trace' => $exception->getTrace(),
            ]
        );
    }

    /**
     * @param Exception $exception
     * @return array
     */
    private function previousExceptionMessages(Exception $exception)
    {
        $messages = [];

        while ($exception = $exception->getPrevious()) {
            $messages[] = $exception->getMessage();
        }

        return $messages;
    }
}
