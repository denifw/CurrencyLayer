<?php

include('CurrencyLayer.php');
$currencyLayer = new CurrencyLayer();
$baseCurrency = 'IDR';
$listForeign  = ['BTC','EUR','USD','GBP'];

$success = $currencyLayer->getLiveCurrency($baseCurrency, $listForeign);
if($success === true) {
    for($i=0; $i < count($listForeign); $i++) {
        echo 'Rate from '.$baseCurrency.' to '.$listForeign[$i].' = '. $currencyLayer->getExchangeRateValue($baseCurrency,$listForeign[$i]).' reverse '.$currencyLayer->getExchangeRateValue($listForeign[$i],$baseCurrency).'<br />';
    }
} else {
    echo $currencyLayer->getErrorMessage();
}


