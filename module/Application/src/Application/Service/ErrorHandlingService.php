<?php

namespace Application\Service;

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

    public function logException(\Exception $exception)
    {
        $this->logger->error(
            $exception->getMessage(),
            $exception->getTrace()
        );
    }
}
