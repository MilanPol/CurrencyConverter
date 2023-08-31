<?php

namespace App\Service;

use App\Client\FloatRatesClient;
use App\DataObject\ExchangeRateDataObject;
use App\Entity\Currency\DefaultCurrency;
use App\Entity\Currency\ExchangeRateCurrency;
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
        $existingEntity->setRate($dataObject->getRate());
        $existingEntity->setInverseRate($dataObject->getInverseRate());
        $existingEntity->setTargetCurrencyCode($dataObject->getCode());
        $existingEntity->setUpdatedOn($dataObject->getUpdatedOn());
        $this->entityManager->persist($existingEntity);
        $this->entityManager->flush();
    }
}
