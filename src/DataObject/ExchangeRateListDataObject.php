<?php

namespace App\DataObject;

use InvalidArgumentException;

class ExchangeRateListDataObject
{
    private string $currencyCode;
    private array $currencyExchangeRates = [];

    public function __construct(
        string $currencyCode,
        array $currencyExchangeRates = []
    ) {
        $this->currencyCode = $currencyCode;
        foreach ($currencyExchangeRates as $currencyExchangeRate) {
            try {
                $this->validate($currencyExchangeRate);
            } catch (InvalidArgumentException $exception) {
                continue;
            }
            $this->currencyExchangeRates[] = $currencyExchangeRate;
        }
    }

    protected function validate($value): void
    {
        if (!$value instanceof ExchangeRateDataObject) {
            throw new InvalidArgumentException(
                'Not an instance of ExchangeRateDataObject'
            );
        }
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getCurrencyExchangeRates(): array
    {
        return $this->currencyExchangeRates;
    }
}
