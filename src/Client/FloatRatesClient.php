<?php

namespace App\Client;

use App\DataObject\ExchangeRateDataObject;
use App\DataObject\ExchangeRateListDataObject;
use DateTime;
use stdClass;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FloatRatesClient extends HttpClient
{
    public function __construct(HttpClientInterface $client, string $domainUrl, ?string $authString)
    {
        parent::__construct($client, $domainUrl, $authString);
    }

    public function getExchangeRatesForCurrency(string $currencyCode): ExchangeRateListDataObject
    {
        $content = $this->get(
            sprintf(
                "/%s.json",
                $currencyCode
            )
        );

        return $this->formatIntoExchangeRateListDataObject($content, $currencyCode);
    }

    private function formatIntoExchangeRateListDataObject(
        string $content,
        string $currencyCode
    ): ExchangeRateListDataObject {
        $contentObjects = json_decode($content);
        $arrayOfDataObjects = [];
        foreach ($contentObjects as $contentObject) {
            $arrayOfDataObjects[] = $this->formatIntoExchangeRateDataObject($contentObject);
        }
        return new ExchangeRateListDataObject(
            $currencyCode,
            $arrayOfDataObjects
        );
    }

    private function formatIntoExchangeRateDataObject(stdClass $contentObject)
    {
        return new ExchangeRateDataObject(
            $contentObject->name,
            $contentObject->code,
            $contentObject->numericCode,
            $contentObject->rate,
            $contentObject->inverseRate,
            new DateTime($contentObject->date),
        );
    }
}
