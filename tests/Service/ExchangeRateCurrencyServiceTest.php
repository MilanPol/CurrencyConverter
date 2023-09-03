<?php

namespace App\Tests\Service;

use App\Client\FloatRatesClient;
use App\DataObject\ExchangeRateDataObject;
use App\DataObject\ExchangeRateListDataObject;
use App\Entity\Currency\DefaultCurrency;
use App\Repository\Currency\DefaultCurrencyRepository;
use App\Repository\Currency\ExchangeRateCurrencyRepository;
use App\Service\ExchangeRateCurrencyService;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class ExchangeRateCurrencyServiceTest extends TestCase
{
    private FloatRatesClient|MockObject $floatsRateClient;
    private EntityManager|MockObject $entityManager;
    private ExchangeRateCurrencyRepository|EntityRepository|MockObject $exchangeRateCurrencyRepository;
    private ExchangeRateCurrencyService $exchangeRateCurrencyService;
    private DefaultCurrencyRepository|EntityRepository|MockObject $defaultCurrencyRepository;

    protected function setUp(): void
    {
        $this->floatsRateClient = $this->createMock(FloatRatesClient::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->exchangeRateCurrencyRepository = $this->createMock(ExchangeRateCurrencyRepository::class);
        $this->defaultCurrencyRepository = $this->createMock(DefaultCurrencyRepository::class);
        $this->entityManager->method('getRepository')
            ->willReturnOnConsecutiveCalls(
                $this->defaultCurrencyRepository,
                $this->exchangeRateCurrencyRepository
            );
        $this->exchangeRateCurrencyService = new ExchangeRateCurrencyService(
            $this->floatsRateClient,
            $this->entityManager
        );
        parent::setUp();
    }


    public function testImportExchangeRateCurrenciesSuccess()
    {
        $this->setUp();
        /** Arrange */
        $expectedDefaultCurrencyCode = 'usd';
        /** @var DefaultCurrency $expectedDefaultCurrency */
        $expectedExchangeRateListDataObject = $this->getExpectedFloatRatesClientData(
            $expectedDefaultCurrencyCode
        );

        $this->defaultCurrencyRepository->expects($this->once())
            ->method('findOneBy')
            ->with(
                [
                    'code' => $expectedDefaultCurrencyCode
                ]
            );

        $this->floatsRateClient->expects($this->once())
            ->method('getExchangeRatesForCurrency')
            ->with($expectedDefaultCurrencyCode)
            ->willReturn($expectedExchangeRateListDataObject);

        /** Act */
        $this->exchangeRateCurrencyService->importExchangeRateCurrencies($expectedDefaultCurrencyCode);
        /** Assert */
    }

    private function getExpectedFloatRatesClientData(string $testDefaultCurrencyCode): ExchangeRateListDataObject
    {
        $json = '{"eur":
        {"code":"EUR",
        "alphaCode":"EUR",
        "numericCode":"978",
        "name":"Euro",
        "rate":0.918656936315,"date":"Wed, 30 Aug 2023 23:55:02 GMT","inverseRate":1.0885456370811},
        "gbp":
        {"code":"GBP",
        "alphaCode":"GBP",
        "numericCode":"826",
        "name":"U.K. Pound Sterling",
        "rate":0.78979809665348,"date":"Wed, 30 Aug 2023 23:55:02 GMT","inverseRate":1.2661463787228}}';

        return $this->formatIntoExchangeRateListDataObject($json, $testDefaultCurrencyCode);
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
        $updatedOn = $this->convertToServerTimeZone(new DateTime($contentObject->date));

        return new ExchangeRateDataObject(
            $contentObject->name,
            $contentObject->code,
            $contentObject->numericCode,
            $contentObject->rate,
            $contentObject->inverseRate,
            $updatedOn,
        );
    }

    private function convertToServerTimeZone(DateTime $dateTime): DateTime
    {
        $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

        return $dateTime;
    }
}
