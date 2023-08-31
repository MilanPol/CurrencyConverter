<?php

namespace App\DataObject;

use DateTime;

class ExchangeRateDataObject
{
    private string $name;
    private string $code;
    private string $numericCode;
    private float $rate;
    private float $inverseRate;
    private DateTime $updatedOn;

    public function __construct(
        string $name,
        string $code,
        string $numericCode,
        float $rate,
        float $inverseRate,
        DateTime $updatedOn
    ) {
        $this->name = $name;
        $this->code = $code;
        $this->numericCode = $numericCode;
        $this->rate = $rate;
        $this->inverseRate = $inverseRate;
        $this->updatedOn = $updatedOn;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getNumericCode(): string
    {
        return $this->numericCode;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getInverseRate(): float
    {
        return $this->inverseRate;
    }

    public function getUpdatedOn(): DateTime
    {
        return $this->updatedOn;
    }
}
