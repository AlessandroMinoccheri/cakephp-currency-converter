<?php

namespace CurrencyConverter\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use CurrencyConverter\CurrencyConverter;

/**
 * CurrencyConverter Component to convert currency.
 *
 * Convert an amount number from one currency to an other currency
 * Return currency rate from one currency to an other
 * output type from HTML to JSON format.
 */
class CurrencyConverterComponent extends Component
{
    /**
     * Using database
     *
     * @var bool
     */
    private $database;

    /**
     * Time interval for refreshing database
     *
     * @var int
     */
    private $refresh;

    /**
     * Number of decimal to use for formatting converted price
     *
     * @var int
     */
    private $decimal;

    /**
     * Number to divise 1 and get the sup step to round price to
     *
     * @var float
     */
    private $round;

    /**
     * Session
     *
     * @var \Cake\Http\Session
     */
    private $session;

    /**
     * CurrencyratesTable Class
     * @var \Cake\ORM\Table
     */
    private $currencyratesTable;

    /**
     * Default CurrencyConverterComponent settings.
     *
     * When calling CurrencyConverterComponent() these settings will be merged with the configuration
     * you provide.
     *
     * - `database` - Mention if Component have to store currency rate in database
     * - `refresh` - Time interval for Component to refresh currency rate in database
     * - `decimal` - Number of decimal to use when formatting amount float number
     * - `round` - Number to divise 1 and get the sup step to round price to (eg: 4 for 0.25 step)
     *
     * @var array
     */
    protected $_defaultConfig = [
        'database' => true,
        'refresh' => 24,
        'decimal' => 2,
        'round' => false,
        'apikey' => '',
    ];

    private $apiKey;

    private $currencyConverter;

    /**
     * @param array $config
     * @return void
     */
    public function initialize(array $config = [])
    {
        $this->database = $this->getConfig('database');
        $this->refresh = $this->getConfig('refresh');
        $this->decimal = $this->getConfig('decimal');
        $this->round = ($this->getConfig('round') !== 0 ? $this->getConfig('round') : false);
        $this->apiKey = $this->getConfig('apikey');

        $this->session = $this->request->getSession();
        $this->currencyratesTable = TableRegistry::get('CurrencyConverter.Currencyrates');
        $this->currencyConverter = new CurrencyConverter($this->apiKey);
    }

    /**
     * Convert method take an amount as first parameter and convert it using $from currency and $to currency.
     *
     * @param float|string $amount the amount to convert.
     * @param string $from currency to convert from
     * @param string $to currency to convert to
     * @return string $amount converted
     */
    public function convert($amount, $from, $to)
    {
        if (!isset($this->apiKey)) {
            throw new \Exception('Api Key not found');
        }

        $amount = floatval($amount);
        $rate = $this->_getRateToUse($from, $to);

        return $this->_formatConvert($rate * $amount);
    }

    /**
     * Rate method return the rate of two currencies
     *
     * @param string $from currency to get the rate from
     * @param string $to currency to get the rate to
     * @return float|null $rate
     */
    public function rate($from, $to)
    {
        return $this->_getRateToUse($from, $to);
    }

    /**
     * getRateToUse return rate to use
     * Using $from and $to parameters representing currency to deal with and the configuration settings
     * This method save or update currencyrates Table if necesseray too.
     *
     * @param string $from currency to get the rate from
     * @param string $to currency to get the rate to
     * @return float|null $rate
     */
    private function _getRateToUse($from, $to)
    {
        $rate = 1;
        if ($from !== $to) {
            if ($this->database) {
                $rate = $this->_getRateFromSession($from, $to);
                if (!$rate) {
                    $rate = $this->_getRateFromDatabase($from, $to);
                }
            } else {
                $rate = $this->_getRateFromAPI($from, $to);
            }
        }

        return $rate;
    }

    /**
     * Format number using configuration
     *
     * @param float number to format
     * @return string formatted number
     */
    private function _formatConvert($number)
    {
        if ($this->round) {
            $n = floor($number);
            $fraction = ($number - $n);
            if ($fraction != 0) {
                $step = 1 / $this->round;
                $decimal = (((int)($fraction / $step) + 1) * $step);
                $number = $n + $decimal;
            }
        }
        $number = number_format($number, $this->decimal);

        return number_format($number, $this->decimal);
    }

    /**
     * Check session to see if rate exists in.
     *
     * @param  string $from currency to get the rate from.
     * @param  string $to currency to get the rate to.
     * @return float|null $rate.
     */
    private function _getRateFromSession($from, $to)
    {
        $session = $this->session->read('CurrencyConverter.' . $from . '-' . $to);
        if ($session) {
            $modified = new Time($session['modified']);
            if ($modified->wasWithinLast($this->refresh . ' hours')) {
                return $rate = $session['rate'];
            }
        }

        return null;
    }

    /**
     * Get a rate from database.
     *
     * It queries currencyratesTable and ...
     * if rate exists and has not to be modified, it returns this rate.
     * if rate exists and has to be modified, it call _getRateFromAPI method to get a fresh rate, then update in table and store in session this rate.
     * if rate does not exist, it call _getRateFromAPI to get a fresh rate, then create in table and store this rate.
     *
     * @param  string $from currency to get the rate from
     * @param  string $to currency to get the rate to
     * @return float|null $rate
     */
    private function _getRateFromDatabase($from, $to)
    {
        $result = $this->currencyratesTable->find('all')->where(['from_currency' => $from, 'to_currency' => $to])->first();
        if ($result) {
            if ($result->get('modified')->wasWithinLast($this->refresh . ' hours')) {
                $rate = $result->get('rate');
                $this->_storeRateInSession($result);
            } else {
                $rate = $this->_getRateFromAPI($from, $to);
                if ($rate) {
                    $result->rate = $rate;
                    $this->currencyratesTable->save($result);
                    $this->_storeRateInSession($result);
                }
            }
        } else {
            $rate = $this->_getRateFromAPI($from, $to);
            if ($rate) {
                $result = $this->currencyratesTable->newEntity([
                    'from_currency' => $from,
                    'to_currency' => $to,
                    'rate' => $rate
                ]);
                $this->currencyratesTable->save($result);
                $this->_storeRateInSession($result);
            }
        }

        return $rate;
    }

    /**
     * Store in session a rate and his modified datetime
     *
     * @param  \Cake\ORM\Entity $entity
     * @return void
     */
    private function _storeRateInSession($entity)
    {
        $this->session->write('CurrencyConverter.' . $entity->get('from_currency') . '-' . $entity->get('to_currency'), [
            'rate' => $entity->get('rate'),
            'modified' => $entity->get('modified')->i18nFormat('yyyy-MM-dd HH:mm:ss')
        ]);
    }

    /**
     * Call free.currencyconverterapi.com API to get a rate for one currency to an other one currency.
     *
     * @param string $from the currency.
     * @param string $to the currency.
     * @return int|null $rate.
     */
    private function _getRateFromAPI($from, $to)
    {
        $rate = null;

        return $this->currencyConverter->getRates($from, $to);
    }

    public function setCurrencyConverter(CurrencyConverter $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }
}
