<?php

namespace App\Command;

use App\Constants\DefaultCurrencyConstants;
use App\DataObject\LogContextDataObject;
use App\Service\ExchangeRateCurrencyService;
use App\Service\LogService;
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
    private LogService $logService;

    public function __construct(
        ExchangeRateCurrencyService $currencyService,
        LogService $logService,
    ) {
        parent::__construct();
        $this->currencyService = $currencyService;
        $this->logService = $logService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputOutput = new SymfonyStyle($input, $output);

        try {
            $this->currencyService->importExchangeRateCurrencies(DefaultCurrencyConstants::DEFAULT_CURRENCY_CODE);
        } catch (Exception $exception) {
            $inputOutput->error($exception->getMessage());
            $logMessage = sprintf(
                'Currency exchange rate import has failed because of the following error %s',
                $exception->getMessage()
            );

            $this->logService->addException(
                get_class($this),
                __FUNCTION__,
                LogContextDataObject::ERROR,
                $logMessage,
                $exception
            );
            $this->logService->logContext();
            return Command::FAILURE;
        }
        $logMessage = 'Exchange rate for currencies have been successfully imported';
        $this->logService->addInfoLog(
            get_class($this),
            __FUNCTION__,
            $logMessage
        );

        $inputOutput->success($logMessage);
        return Command::SUCCESS;
    }
}
