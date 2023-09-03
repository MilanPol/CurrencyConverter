<?php

namespace App\Service;

use App\Client\FloatRatesClient;
use App\Constants\DefaultCurrencyConstants;
use App\DataObject\ExchangeRateDataObject;
use App\Entity\Currency\DefaultCurrency;
use App\Entity\Currency\ExchangeRateCurrency;
use App\Repository\Currency\ExchangeRateCurrencyRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class ExchangeRateCurrencyService
{
    private FloatRatesClient $floatRatesClient;
    private EntityManagerInterface $entityManager;

    public function __construct(
        FloatRatesClient $floatRatesClient,
        EntityManagerInterface $entityManager,
    ) {
        $this->floatRatesClient = $floatRatesClient;
        $this->entityManager = $entityManager;
    }

    public function importExchangeRateCurrencies(string $defaultCurrency): bool
    {
        $currencyExchangeRateList = $this->floatRatesClient->getExchangeRatesForCurrency($defaultCurrency);
        $defaultCurrencyEntity = $this->entityManager->getRepository(DefaultCurrency::class)->findOneBy(
            [
                'code' => $defaultCurrency
            ]
        );
        if (!$defaultCurrencyEntity) {
            $defaultCurrencyEntity = $this->createDefaultCurrency($defaultCurrency);
        }

        /** @var ExchangeRateCurrencyRepository $exchangeRateCurrencyRepository */
        $exchangeRateCurrencyRepository = $this->entityManager->getRepository(ExchangeRateCurrency::class);
        $defaultExchangeCurrency = $exchangeRateCurrencyRepository->findOneBy(
            [
                'targetCurrencyCode' => strtoupper($defaultCurrencyEntity->getCode()),
                'defaultCurrency' => $defaultCurrencyEntity
            ]
        );

        if (!$defaultExchangeCurrency) {
            $this->createDefaultCurrencyExchangeRateCurrency($defaultCurrencyEntity);
        }

        /** @var ExchangeRateDataObject $exchangeRateDataObject */
        foreach ($currencyExchangeRateList->getCurrencyExchangeRates() as $exchangeRateDataObject) {
            $existingEntity = $this->entityManager->getRepository(ExchangeRateCurrency::class)->findOneBy(
                [
                    'targetCurrencyCode' => $exchangeRateDataObject->getCode(),
                    'defaultCurrency' => $defaultCurrencyEntity
                ]
            );
            if ($existingEntity) {
                $this->updateExchangeRateCurrency($existingEntity, $exchangeRateDataObject);
                continue;
            }
            $this->createExchangeRateCurrency($exchangeRateDataObject, $defaultCurrencyEntity);
        }
        return true;
    }

    private function createExchangeRateCurrency(
        ExchangeRateDataObject $dataObject,
        DefaultCurrency $defaultCurrency
    ): void {
        $exchangeRateEntity = new ExchangeRateCurrency();
        $exchangeRateEntity->setFullName($dataObject->getName());
        $exchangeRateEntity->setRate($dataObject->getRate());
        $exchangeRateEntity->setInverseRate($dataObject->getInverseRate());
        $exchangeRateEntity->setTargetCurrencyCode($dataObject->getCode());
        $exchangeRateEntity->setUpdatedOn($dataObject->getUpdatedOn());
        $exchangeRateEntity->setDefaultCurrency($defaultCurrency);
        $this->entityManager->persist($exchangeRateEntity);
        $this->entityManager->flush();
    }

    private function createDefaultCurrency(
        string $defaultCurrency
    ): DefaultCurrency {
        $defaultCurrencyEntity = new DefaultCurrency();
        $defaultCurrencyEntity->setCode($defaultCurrency);
        $this->entityManager->persist($defaultCurrencyEntity);
        $this->entityManager->flush();

        return $defaultCurrencyEntity;
    }

    private function updateExchangeRateCurrency(
        ExchangeRateCurrency $existingEntity,
        ExchangeRateDataObject $dataObject
    ): void {
        $existingEntity->setFullName($dataObject->getName());
        $existingEntity->setRate($dataObject->getRate());
        $existingEntity->setInverseRate($dataObject->getInverseRate());
        $existingEntity->setTargetCurrencyCode($dataObject->getCode());
        $existingEntity->setUpdatedOn($dataObject->getUpdatedOn());
        $this->entityManager->persist($existingEntity);
        $this->entityManager->flush();
    }

    private function createDefaultCurrencyExchangeRateCurrency(
        DefaultCurrency $defaultCurrency
    ): void {
        $exchangeRateEntity = new ExchangeRateCurrency();
        $exchangeRateEntity->setFullName(DefaultCurrencyConstants::DEFAULT_CURRENCY_NAME);
        $exchangeRateEntity->setRate(1);
        $exchangeRateEntity->setInverseRate(1);
        $exchangeRateEntity->setTargetCurrencyCode(strtoupper($defaultCurrency->getCode()));
        $exchangeRateEntity->setUpdatedOn(new DateTime());
        $exchangeRateEntity->setDefaultCurrency($defaultCurrency);
        $this->entityManager->persist($exchangeRateEntity);
        $this->entityManager->flush();
    }

    public function getExchangeRatesForCurrencyByAmount(
        ExchangeRateCurrency $fromExchangeCurrency,
        float $amount,
        ArrayCollection $toExchangeCurrencies
    ): array {

        $toExchangeRateCalculated = [];
        /** @var ExchangeRateCurrency $toExchangeCurrency */
        foreach ($toExchangeCurrencies as $toExchangeCurrency) {
            $toExchangeRateCalculated[
                $toExchangeCurrency->getFullName()
            ] = $this->calculateAmountFromExchangeRateToExchangeRate(
                $amount,
                $fromExchangeCurrency,
                $toExchangeCurrency
            );
        }

        return $toExchangeRateCalculated;
    }

    private function calculateAmountFromExchangeRateToExchangeRate(
        float $amount,
        ExchangeRateCurrency $fromExchangeCurrency,
        ExchangeRateCurrency $toExchangeCurrency
    ): float {
        $dollarAmount = $fromExchangeCurrency->getInverseRate() * $amount;

        if (
            $toExchangeCurrency->getTargetCurrencyCode() === strtoupper(
                DefaultCurrencyConstants::DEFAULT_CURRENCY_CODE
            )
        ) {
            return $dollarAmount;
        }
        return $toExchangeCurrency->getRate() * $dollarAmount;
    }
}
