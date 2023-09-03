<?php

namespace App\Service;

use App\DataObject\LogContextDataObject;
use Exception;
use Psr\Log\LoggerInterface;

class LogService
{
    private LoggerInterface $logger;

    private array $contextObjects;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->contextObjects = [];
    }

    private function addContext(LogContextDataObject $logContextDataObject)
    {
        $this->contextObjects[] = $logContextDataObject;
    }

    public function logContext(): void
    {
        /** @var LogContextDataObject $contextObject */
        foreach ($this->contextObjects as $contextObject) {
            $this->logger->log(
                $contextObject->getLevel(),
                $contextObject->formatIntoLogMessage()
            );
        }

        $this->contextObjects = [];
    }

    private function createContext(
        string $className,
        string $functionName,
        string $logLevel,
        string $logMessage,
        ?Exception $exception
    ): LogContextDataObject {
        $trace = '';
        if ($exception) {
            $trace = $exception->getTraceAsString();
        }

        return new LogContextDataObject(
            $className,
            $functionName,
            $logLevel,
            $logMessage,
            $trace,
        );
    }

    public function addException(
        string $className,
        string $functionName,
        string $logLevel,
        string $logMessage,
        Exception $exception,
    ): void {
        $context = $this->createContext(
            $className,
            $functionName,
            $logLevel,
            $logMessage,
            $exception
        );

        $this->addContext($context);
    }

    public function addInfoLog(
        string $className,
        string $functionName,
        string $logMessage
    ): void {
        $context = $this->createContext(
            $className,
            $functionName,
            LogContextDataObject::INFO,
            $logMessage,
            null
        );

        $this->addContext($context);
    }
}
