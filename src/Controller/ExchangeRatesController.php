<?php

namespace App\Controller;

use App\Entity\ExchangeRate;
use App\Form\ExchangeRateFormType;
use App\Form\ExchangeRatesFormDatesScopeType;
use App\Services\ExchangeRatesCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
class ExchangeRatesController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function main(): Response
    {
        
        //echo $this->getParameter('api_key'); exit;
        return $this->render('base.html.twig');
    }
    /**
     * @Route("/exchange-rate", name="app_exchange_rates")
     */
    public function getExchangeRate(Request $request, ExchangeRatesCalculator $exchangeRatesCalculator, ValidatorInterface $validator): Response
    {
        $exchangeRate = new ExchangeRate();
        
        $exchangeRateForm = $this->createForm(ExchangeRateFormType::class, $exchangeRate);
        $exchangeRateForm->handleRequest($request);

        //$errors = $validator->validate($exchangeRate);

        
        if ($exchangeRateForm->isSubmitted() && $exchangeRateForm->isValid()) {
            
            return $this->render('exchange_rates/exchangeRate.html.twig', [
                'controller_name' => 'ExchangeRatesController',
                'form' => $exchangeRateForm->createView(),
                'result' =>  $exchangeRatesCalculator->calculateFromOneCurrencyToAnother($exchangeRate, $this->getParameter('api_key')),
                'typeCurrency' => $exchangeRate->getFinalCurrency()
            ]);
        }
        
        return $this->render('exchange_rates/exchangeRate.html.twig', [
            'controller_name' => 'ExchangeRatesController',
            'form' => $exchangeRateForm->createView(),
            'result' => 0
        ]);
    }
     /**
     * @Route("/exchange-rate-date", name="app_exchange_rates_date")
     */
    public function getDifferencesDateScopeExchangeRate(Request $request, ExchangeRatesCalculator $exchangeRatesCalculator): Response
    {
        $exchangeRate = new ExchangeRate($this->getParameter('api_key'));
        
        $exchangeRateDatesScopeForm = $this->createForm(ExchangeRatesFormDatesScopeType::class, $exchangeRate);
        $exchangeRateDatesScopeForm->handleRequest($request);

        if ($exchangeRateDatesScopeForm->isSubmitted() && $exchangeRateDatesScopeForm->isValid()) {
            $results = $exchangeRatesCalculator->calculateDifferencesExchangeRatesDateScope($exchangeRate, $this->getParameter('api_key'));
            return $this->render('exchange_rates/exchangeRateDate.html.twig', [
                'controller_name' => 'ExchangeRatesController',
                'form2' => $exchangeRateDatesScopeForm->createView(),
                'startDatePrice' =>  $results[0],
                'endDatePrice' =>  $results[1],
                'difference' => $results[2],
                'info' => $results[3],
            ]);
        }
        return $this->render('exchange_rates/exchangeRateDate.html.twig', [
            'controller_name' => 'ExchangeRatesController',
            'form2' => $exchangeRateDatesScopeForm->createView(),
            'result2' => 0,
            'info' => ""
        ]);
    }
}
