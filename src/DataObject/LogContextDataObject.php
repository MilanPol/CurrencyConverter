<?php

namespace App\DataObject;

use App\Utilities\UuidGenerator;
use DateTime;

class LogContextDataObject extends ServiceDataObject
{
    public const FATAL = 'FATAL';
    public const ERROR = 'ERROR';
    public const ALERT = 'ALERT';
    public const INFO = 'INFO';

    private string $serviceName;
    private string $methodName;
    private string $logMessage;
    private string $level;
    private string $callStack;
    private string $logTimeStamp;
    private string $id;

    public function __construct(
        string $serviceName,
        string $methodName,
        string $logLevel,
        string $logMessage,
        string $callStack,
    ) {
        $timeStamp = new DateTime();
        $this->id = UuidGenerator::generateUUID16bit();
        $this->logTimeStamp = $timeStamp->format('d-m-y H:i:s');
        $this->serviceName = $serviceName;
        $this->methodName = $methodName;
        $this->logMessage = $logMessage;
        $this->callStack = $callStack;
        $this->level = $logLevel;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getCallStack(): string
    {
        return $this->callStack;
    }

    public function getLogTimeStamp(): string
    {
        return $this->logTimeStamp;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getLogMessage(): string
    {
        return $this->logMessage;
    }

    public function formatIntoLogMessage(): string
    {
        $serializedObject = [
                'serviceName' => $this->serviceName,
                'methodName' => $this->methodName,
                'logMessage' => $this->logMessage,
                'level' => $this->level,
                'logTimeStamp' => $this->logTimeStamp,
                'id' => $this->id,
                'callStack' => $this->callStack
        ];

        return json_encode($serializedObject);
    }
}
