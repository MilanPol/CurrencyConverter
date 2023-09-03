<?php

declare(strict_types=1);

namespace App\Entity\Currency;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Currency\ExchangeRateCurrencyRepository")
 */
class ExchangeRateCurrency
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="float")
     */
    private float $rate;
    /**
     * @ORM\Column(type="float")
     */
    private float $inverseRate;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $updatedOn;


    /**
     * @ORM\Column(type="string", nullable="false")
     */
    private ?string $targetCurrencyCode;


    /**
     * @ORM\Column(type="string", nullable="false")
     */
    private ?string $fullName;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="DefaultCurrency",
     *     inversedBy="games"
     * )
     */
    public ?DefaultCurrency $defaultCurrency = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }

    public function getInverseRate(): float
    {
        return $this->inverseRate;
    }

    public function setInverseRate(float $inverseRate): void
    {
        $this->inverseRate = $inverseRate;
    }

    public function getUpdatedOn(): DateTime
    {
        return $this->updatedOn;
    }

    public function setUpdatedOn(DateTime $updatedOn): void
    {
        $this->updatedOn = $updatedOn;
    }

    public function getTargetCurrencyCode(): ?string
    {
        return $this->targetCurrencyCode;
    }

    public function setTargetCurrencyCode(?string $targetCurrencyCode): void
    {
        $this->targetCurrencyCode = $targetCurrencyCode;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getDefaultCurrency(): ?DefaultCurrency
    {
        return $this->defaultCurrency;
    }

    public function setDefaultCurrency(?DefaultCurrency $defaultCurrency): void
    {
        $this->defaultCurrency = $defaultCurrency;
        $this->defaultCurrency->addExchangeRateCurrency($this);
    }
}
