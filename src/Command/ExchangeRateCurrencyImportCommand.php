<?php

namespace App\Command;

use App\Constants\DefaultCurrencyConstants;
use App\Service\ExchangeRateCurrencyService;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'import:exchange-rate:currencies',
    description: 'Import current currency exchange rates',
)]
class ExchangeRateCurrencyImportCommand extends Command
{
    private ExchangeRateCurrencyService $currencyService;

    public function __construct(
        ExchangeRateCurrencyService $currencyService
    ) {
        parent::__construct();
        $this->currencyService = $currencyService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputOutput = new SymfonyStyle($input, $output);

        try {
            $this->currencyService->importExchangeRateCurrencies(DefaultCurrencyConstants::DEFAULT_CURRENCY_CODE);
        } catch (Exception $exception) {
            $inputOutput->error($exception->getMessage());
        }
        $inputOutput->success('Exchange rate for currencies have been successfully imported');

        return Command::SUCCESS;
    }
}
