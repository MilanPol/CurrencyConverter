<?php

namespace App\Controller\Admin;

use App\Form\ExchangeRateCompareFormType;
use App\Service\ExchangeRateCurrencyService;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ExchangeRateCurrencyController extends AbstractDashboardController
{
    private ExchangeRateCurrencyService $exchangeRateCurrencyService;

    public function __construct(
        ExchangeRateCurrencyService $exchangeRateCurrencyService
    ) {
        $this->exchangeRateCurrencyService = $exchangeRateCurrencyService;
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/currency-exchange', name: 'currency-exchange')]
    public function index(): Response
    {
        return $this->render('currency/index.html.twig');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboard/currency-exchange-action', name: 'currency-exchange-action')]
    public function convertExchangeRateCurrencyAction(Request $request)
    {
        $form = $this->createForm(ExchangeRateCompareFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $exchangeRatesByCurrencies = $this->exchangeRateCurrencyService->getExchangeRatesForCurrencyByAmount(
                $data['fromExchangeRateCurrency'],
                $data['amount'],
                $data['toExchangeRateCurrencies']
            );

            return $this->render(
                'currency/index.html.twig',
                [
                    "form" => $form,
                    "exchangeRateByCurrencies" => $exchangeRatesByCurrencies
                ]
            );
        }

        return $this->render(
            'currency/index.html.twig',
            [
                "form" => $form
            ]
        );
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
    }
}
