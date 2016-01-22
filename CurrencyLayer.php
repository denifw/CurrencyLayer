<?php

/**
 * Class CurrencyLayer
 */
class CurrencyLayer
{

    /**
     * Property basic URL for currency layer api.
     *
     * @var string $baseURL To store base url for the api.
     */
    protected $baseURL = 'http://apilayer.net/api/';

    /**
     * Property for access key currency layer api.
     *
     * @var string $apiKey To store access key for currency layer api.
     */
    protected $apiKey = 'f8ab3e7344c72df331847d71bf898781';

    /**
     * Property for api plan currency layer api.
     *
     * @var string $apiPlan To store api plan for currency layer api.
     */
    protected $apiPlan = 'free';

    /**
     * Property for default source currency layer api.
     * Default source is the only one source or base currency that we can use in the request if the api plan in the free level.
     *
     * @var string $defaultSourceApi To store default source currency layer api.
     */
    protected $defaultSourceApi = 'USD';

    /**
     * Property to store response from api.
     *
     * @var array $apiResponse To store response from api.
     */
    private $responseApi;

    /**
     * Property to store error code from api.
     *
     * @var string $errorCode To store error code from api.
     */
    private $errorCode;

    /**
     * Property to store error message from api.
     *
     * @var string $errorMessage To store error message from api.
     */
    private $errorMessage;

    /**
     * Property to store base currency.
     *
     * @var string $baseCurrency To store base currency.
     */
    private $baseCurrency;

    /**
     * Property to store list foreign currency.
     *
     * @var array $listForeignCurrency To store list of foreign currency.
     */
    private $listForeignCurrency;

    /**
     * Property to store currency exchange rate.
     *
     * @var array $listExchangeRate To store currency exchange rate.
     */
    private $listExchangeRate;

    /**
     * Property to store time update of exchange rate.
     *
     * @var string $time To store time update of exchange rate.
     */
    private $time;

    /**
     * CurrencyLayer constructor.
     */
    public function __construct()
    {
        $this->baseCurrency = 'USD';
        $this->listForeignCurrency = [];
        $this->listExchangeRate = [];
    }

    /**
     * Function to get real time/live update currency from currency layer  api.
     *
     * @param string $baseCurrency        To store base currency.
     * @param array  $listForeignCurrency To store list of tha foreign currency.
     *
     * @return boolean
     */
    public function getLiveCurrency($baseCurrency, $listForeignCurrency)
    {
        if (strlen(trim($baseCurrency)) > 0 and is_array($listForeignCurrency) === true and count($listForeignCurrency) > 0) {
            # set the parameter into class property.
            $this->baseCurrency = strtoupper(trim($baseCurrency));
            $this->listForeignCurrency = $listForeignCurrency;
            # Generate the request url.
            $requestUrl = $this->baseURL . 'live?access_key=' . $this->apiKey;
            if (strtolower($this->apiPlan) === 'free') {
                $requestUrl .= '&source=' . $this->defaultSourceApi . '&currencies=' . implode(',', $listForeignCurrency) . ',' . $this->baseCurrency;
            } else {
                $requestUrl .= '&source=' . $this->baseCurrency . '&currencies=' . implode(',', $listForeignCurrency);
            }
            # send request to api layer and return the result.
            return $this->doRequest($requestUrl);
        } else {
            $this->errorMessage = 'Invalid parameter given. please check the documentation block.';
            return false;
        }
    }

    /**
     * Function to get historical currency from currency layer api.
     *
     * @param string    $baseCurrency        To store base currency.
     * @param array     $listForeignCurrency To store list of tha foreign currency with format array string.
     * @param \Datetime $date                To set specific date of currency exchange.
     *
     * @return boolean
     */
    public function getHistoricalCurrency($baseCurrency, $listForeignCurrency, $date)
    {
        if (strlen(trim($baseCurrency)) > 0 and is_array($listForeignCurrency) === true and count($listForeignCurrency) > 0 and $date instanceof \DateTime) {
            # set the parameter into class property.
            $this->baseCurrency = strtoupper(trim($baseCurrency));
            $this->listForeignCurrency = $listForeignCurrency;
            # Generate the request url.
            $requestUrl = $this->baseURL . 'historical?access_key=' . $this->apiKey;
            if (strtolower($this->apiPlan) === 'free') {
                $requestUrl .= '&source=' . $this->defaultSourceApi . '&currencies=' . implode(',', $listForeignCurrency) . ',' . $this->baseCurrency . '&date=' . $date->format('Y-m-d');
            } else {
                $requestUrl .= '&source=' . $this->baseCurrency . '&currencies=' . implode(',', $listForeignCurrency) . '&date=' . $date->format('Y-m-d');
            }
            # send request to api layer and return the result.
            return $this->doRequest($requestUrl);
        } else {
            $this->errorMessage = 'Invalid parameter given. please check the documentation block.';
            return false;
        }
    }

    /**
     * Function to get exchange rate value.
     *
     * @param string $from To store base currency.
     * @param string $to   To store foreign curency.
     *
     * @return float
     */
    public function getExchangeRateValue($from, $to)
    {
        $baseCurrency = strtoupper(trim($from));
        $foreignCurrency = strtoupper(trim($to));
        if (count($this->listExchangeRate) <= 0 or $this->listExchangeRate === null) {
            $this->getLiveCurrency($baseCurrency, [$foreignCurrency]);
        }
        $key = $baseCurrency . $foreignCurrency;
        if (array_key_exists($key, $this->listExchangeRate) === true) {
            return $this->listExchangeRate[$key];
        } else {
            return -1;
        }
    }

    /**
     * Function to get value of response api.
     *
     * @return array
     */
    public function getResponseApi()
    {
        return $this->responseApi;
    }

    /**
     * Function to get error code from api layer.
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Function to get error message from the request.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Function to get base currency value.
     *
     * @return string
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * Function to get list of foreign currency.
     *
     * @return array
     */
    public function getListForeignCurrency()
    {
        return $this->listForeignCurrency;
    }

    /**
     * Function to get list of exchange rate.
     *
     * @return array
     */
    public function getListExchangeRate()
    {
        return $this->listExchangeRate;
    }

    /**
     * Function to get time value.
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Sent request to currency layer api and handle the response.
     *
     * @param string $requestUrl To store the url request to the currency layer.
     *
     * @return boolean
     */
    private function doRequest($requestUrl)
    {
        # Send request to http://apilayer.net/api/
        $response = file_get_contents($requestUrl);
        # decode the response into array format
        $this->responseApi = json_decode($response, true);
        # Do handle response.
        $this->doHandleApiResponse();
        # return the success value.
        return $this->responseApi['success'];
    }

    /**
     * Function to handle response from api layer and store it into the class attribute.
     *
     * @return void
     */
    private function doHandleApiResponse()
    {
        if ($this->responseApi['success'] === true) {
            # get the time update of  exchange rate.
            $this->time = \DateTime::createFromFormat('U', $this->responseApi['timestamp'])->format('d/m/Y H:i:s');
            # get the exchange rate.
            $quotes = $this->responseApi['quotes'];
            # get the length of foreign currency data.
            $lengthForeignCurrency = count($this->listForeignCurrency);
            for ($indexForeign = 0; $indexForeign < $lengthForeignCurrency; $indexForeign++) {
                if (strtolower($this->apiPlan) === 'free') {
                    # Do convert rate from USD to base currency if the api plan = free.
                    $this->doConvertRateFromDefaultSourceToBaseCurrency($quotes, $this->listForeignCurrency[$indexForeign]);
                } else {
                    # Set the array key for quotes.
                    $keyBaseToForeign = $this->baseCurrency . $this->listForeignCurrency[$indexForeign];
                    $keyForeignToBase = $this->listForeignCurrency[$indexForeign] . $this->baseCurrency;
                    #set the data.
                    if (array_key_exists($keyBaseToForeign, $quotes) === true) {
                        $foreignRate = $quotes[$keyBaseToForeign];
                        $this->listExchangeRate[$keyBaseToForeign] = number_format($foreignRate, 6, '.', '');
                        $this->listExchangeRate[$keyForeignToBase] = number_format(1 / $foreignRate, 6, '.', '');
                    } else {
                        $this->listExchangeRate[$keyBaseToForeign] = -1;
                        $this->listExchangeRate[$keyForeignToBase] = -1;
                    }
                }
            }
        } else {
            $this->errorCode = $this->responseApi['error']['code'];
            $this->errorMessage = $this->responseApi['error']['info'];
        }
    }

    /**
     * Function to convert the rate from default source into base currency.
     *
     * @param array  $quotes          To store list of exchange rate from api.
     * @param string $foreignCurrency To store the foreign currency.
     *
     * @return void
     */
    private function doConvertRateFromDefaultSourceToBaseCurrency($quotes, $foreignCurrency)
    {
        /*
         * Form ex :
         * a source = b base
         * a source = c foreign
         * 1 IDR = ((a/b) * (c/a)) EUR
         * 1 EUR = (1/((a/b) * (c/a))) IDR
         * */
        # Generate array key
        $keySourceBase = strtoupper($this->defaultSourceApi) . $this->baseCurrency;
        $keySourceForeign = strtoupper($this->defaultSourceApi) . $foreignCurrency;
        # Cek if the array key exist or not.
        if (array_key_exists($keySourceBase, $quotes) === true and array_key_exists($keySourceForeign, $quotes) === true) {
            # Get the rate from source to Base currency.
            $rateSourceToBaseCurrency = $quotes[$keySourceBase];
            # Get the rate from source to Foreign currency.
            $rateSourceToForeignCurrency = $quotes[$keySourceForeign];
            # reverse rate from source to base.
            $rateBaseCurrencyToSource = 1 / $rateSourceToBaseCurrency;
            # set rate from base to foreign.
            $this->listExchangeRate[$this->baseCurrency . $foreignCurrency] = number_format(1 / ($rateBaseCurrencyToSource * $rateSourceToForeignCurrency), 6, '.', '');
            # set rate from foreign to base.
            $this->listExchangeRate[$foreignCurrency . $this->baseCurrency] = number_format($rateBaseCurrencyToSource * $rateSourceToForeignCurrency, 6, '.', '');
        } else {
            # set rate from base to foreign.
            $this->listExchangeRate[$this->baseCurrency . $foreignCurrency] = -1;
            # set rate from foreign to base.
            $this->listExchangeRate[$foreignCurrency . $this->baseCurrency] = -1;
        }
    }
}
