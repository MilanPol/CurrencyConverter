<?php

namespace App\Entity\Currency;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class DefaultCurrency
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", nullable="false")
     */
    private ?string $code;

    /**
     * @ORM\OneToMany(
     *     targetEntity="ExchangeRateCurrency",
     *     mappedBy="defaultCurrency",
     *     cascade={"persist","remove"}
     * )
     */
    public iterable $exchangeRateCurrencies;

    public function __construct()
    {
        $this->exchangeRateCurrencies = new ArrayCollection();
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function addExchangeRateCurrency(ExchangeRateCurrency $exchangeRateCurrency)
    {
        $this->exchangeRateCurrencies->add($exchangeRateCurrency);
    }

    public function removeExchangeRateCurrency(ExchangeRateCurrency $exchangeRateCurrency)
    {
        $this->exchangeRateCurrencies->removeElement($exchangeRateCurrency);
    }

    public function getExchangeRateCurrencies(): iterable|ArrayCollection
    {
        return $this->exchangeRateCurrencies;
    }
}
