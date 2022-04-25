<?php 

namespace App\Services;

use App\Entity\ExchangeRate;
use DateTime;

class ExchangeRatesCalculator {
    
    private $endpoint = 'latest';
    
    const MAX_PERCENTAGE = 100; 

    public function calculateFromOneCurrencyToAnother(ExchangeRate $exchangeRate, $access_key) {
        return $this->getLatestExchangeRateData($exchangeRate, $access_key);
    }

    public function calculateDifferencesExchangeRatesDateScope(ExchangeRate $exchangeRate, $access_key) {
        $exchangeRateStartDateResult = $this->getHistoricalExchangeRatesForSpecificDate($exchangeRate, $exchangeRate->getStartDate(), $access_key);
        $exchangeRateEndDateResult = $this->getHistoricalExchangeRatesForSpecificDate($exchangeRate, $exchangeRate->getEndDate(), $access_key);
        if (round($exchangeRateStartDateResult[$exchangeRate->getFinalCurrency()],2) > round($exchangeRateEndDateResult[$exchangeRate->getFinalCurrency()],2)) {
            $result = "mniej";
            $percentageChange = round(( (($exchangeRateEndDateResult[$exchangeRate->getFinalCurrency()] * self::MAX_PERCENTAGE) / $exchangeRateStartDateResult[$exchangeRate->getFinalCurrency()]) - self::MAX_PERCENTAGE),2);
        } else if (round($exchangeRateStartDateResult[$exchangeRate->getFinalCurrency()],2) === round($exchangeRateEndDateResult[$exchangeRate->getFinalCurrency()],2)) {
            $result = "tyle samo";
            $percentageChange =  0;
        }
        else {
            $result = "więcej";
            $percentageChange = round(((self::MAX_PERCENTAGE - (($exchangeRateStartDateResult[$exchangeRate->getFinalCurrency()]) * self::MAX_PERCENTAGE) / $exchangeRateEndDateResult[$exchangeRate->getFinalCurrency()])),2);
        }
        
        return [round($exchangeRateStartDateResult[$exchangeRate->getFinalCurrency()],2) ,round($exchangeRateEndDateResult[$exchangeRate->getFinalCurrency()],2) , $percentageChange , $result];
    }
    private function getLatestExchangeRateData(ExchangeRate $exchangeRate, $access_key) {
            // Initialize CURL:
            //here add base 
            
            $ch = curl_init('http://api.exchangeratesapi.io/v1/'.$this->endpoint.'?access_key='.$access_key);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);
            // Decode JSON response:
            $exchangeRates = json_decode($json, true);
            // Access the exchange rate values, e.g. GBP:;
            if (!array_key_exists($exchangeRate->getStartingCurrency(),$exchangeRates['rates']) ||!array_key_exists($exchangeRate->getFinalCurrency(),$exchangeRates['rates']) ) {
                return null; //think about it
            }
            return round(($exchangeRates['rates'][$exchangeRate->getFinalCurrency()] / $exchangeRates['rates'][$exchangeRate->getStartingCurrency()]) * $exchangeRate->getAmount(), 2);
    }
    private function getHistoricalExchangeRatesForSpecificDate(ExchangeRate $exchangeRate, DateTime $date, $access_key) {
        // Initialize CURL:
            //here add base 
            $ch = curl_init('http://api.exchangeratesapi.io/v1/'.$date->format('Y-m-d').'?access_key='. $access_key);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);
            // Decode JSON response:
            $exchangeRates = json_decode($json, true);
            // Access the exchange rate values, e.g. GBP:
            if (!array_key_exists($exchangeRate->getStartingCurrency(),$exchangeRates['rates']) ||!array_key_exists($exchangeRate->getFinalCurrency(),$exchangeRates['rates']) ) {
                return null; //think about it
            }
            return $exchangeRates['rates'];
    }
    
}