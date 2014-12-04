<?php
/**
 * Created by Gary Hockin.
 * Date: 04/12/14
 * @GeeH
 */

namespace Application\Service;


use Zend\Log\Logger;

class ErrorHandlingService
{

    /**
     * @var Logger
     */
    protected $logger;

    function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    function logException(\Exception $e)
    {
        $trace = $e->getTraceAsString();
        $i     = 1;
        do {
            $messages[] = $i++ . ": " . $e->getMessage();
        } while ($e = $e->getPrevious());

        $log = "Exception:n" . implode("n", $messages);
        $log .= "nTrace:n" . $trace;

        $this->logger->err($log);
    }
}
