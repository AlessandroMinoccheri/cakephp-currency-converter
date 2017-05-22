<?php

namespace CurrencyConverter\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Database\Schema\TableSchema;

class CurrencyConverterComponent extends Component
{
    public $controller = null;

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function startup($event)
    {
        $this->setController($event->subject());
    }

    /**
     * Convertion function
     *
     * @param string $fromCurrency the starting currency that user wants to convert to.
     * @param string $toCurrency the ending currency that user wants to convert to.
     * @param float $amount the amount to convert.
     * @param boolean $saveIntoDb if develop wants to store convertion rate for use it without resending data to yahoo service.
     * @param int $hourDifference the hour difference to check if the last convertion is passed, if yes make a new call to yahoo finance api.
     * @param string $dataSource which dataSOurce need to use
     * @return float the total amount converted into the new currency
     */
    public function convert($fromCurrency, $toCurrency, $amount, $saveIntoDb = 1, $hourDifference = 1, $dataSource = 'default') {
        if($fromCurrency != $toCurrency){
            $rate = 0;

            if ($fromCurrency == "PDS"){
                $fromCurrency = "GBP";
            }
            
            if($saveIntoDb == 1){
                $this->checkIfExistTable($dataSource);

                $CurrencyConverter = TableRegistry::get('CurrencyConverter', [
                    'className' => 'CurrencyConverter\Model\Table\CurrencyConvertersTable',
                    'table' => 'currency_converter'
                ]);
                
                $arrReturn = $this->checkToFind($fromCurrency, $toCurrency, $hourDifference);
                if(isset($arrReturn['find'])){
                    $find = $arrReturn['find'];
                }

                if(isset($arrReturn['rate'])){
                    $rate = $arrReturn['rate'];
                }

                if($find == 0){
                    $rate = $this->getRates($fromCurrency, $toCurrency);

                    $data = [
                        'fromCurrency' => $fromCurrency,
                        'toCurrency'   => $toCurrency,
                        'rates'        => $rate,
                        'created'      => date('Y-m-d H:i:s'),
                        'modified'     => date('Y-m-d H:i:s'),
                    ];

                    $entity = $CurrencyConverter->newEntity($data);
                    $CurrencyConverter->save($entity);
                }

                $value = (double)$rate * (double)$amount;
                return number_format((double)$value, 2, '.', '');
            }
            else{
                $rate = $this->getRates($fromCurrency, $toCurrency);
                $value = (double)$rate * (double)$amount;
                return number_format((double)$value, 2, '.', '');
            }
        }
        else{
            return number_format((double)$amount, 2, '.', '');
        }
    }

    /**
     * Convertion function call to yahoo finance api
     *
     * @param string $fromCurrency the starting currency that user wants to convert to.
     * @param string $toCurrency the ending currency that user wants to convert to.
     * @param int $hourDifference the hour difference to check if the last convertion is passed, if yes make a new call to yahoo finance api.
     * @return int if it's finded value
     */
    public function checkToFind ($fromCurrency, $toCurrency, $hourDifference) {
        $arrReturn = array();
        $find = 0;
        $rate = 0;

        $CurrencyConverter = TableRegistry::get('CurrencyConverter', [
            'className' => 'CurrencyConverter\Model\Table\CurrencyConvertersTable',
            'table' => 'currency_converter'
        ]);

        $result = $CurrencyConverter->find('all')
            ->where(['fromCurrency' => $fromCurrency, 'toCurrency' => $toCurrency ]);

        foreach ($result as $row){
            $find = 1;
            $lastUpdated = $row['CurrencyConverter']['modified'];
            $now = date('Y-m-d H:i:s');
            $dStart = new \DateTime($now);
            $dEnd = new \DateTime($lastUpdated);
            $diff = $dStart->diff($dEnd);

            if(((int)$diff->y >= 1) || ((int)$diff->m >= 1) || ((int)$diff->d >= 1) || ((int)$diff->h >= $hourDifference) || ((double)$row['CurrencyConverter']['rates'] == 0)){
                $rate = $this->getRates($fromCurrency, $toCurrency);

                $data = [
                    'fromCurrency'        => $fromCurrency,
                    'toCurrency'          => $toCurrency,
                    'rates'       => $rate,
                    'modified'    => date('Y-m-d H:i:s'),
                ];
                $entity = $CurrencyConverter->newEntity($data);
                $CurrencyConverter->save($entity);
            }
            else{
                $rate = $row['CurrencyConverter']['rates'];
            }
        }

        $arrReturn['find'] = $find;
        $arrReturn['rate'] = $rate;

        return($arrReturn);
    }

    /**
     * Convertion function call to yahoo finance api
     *
     * @param string $fromCurrency the starting currency that user wants to convert to.
     * @param string $toCurrency the ending currency that user wants to convert to.
     * @return float the rate of convertion
     */
    private function getRates($fromCurrency, $toCurrency){
        $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $fromCurrency . $toCurrency .'=X';
        $handle = @fopen($url, 'r');
         
        if ($handle) {
            $result = fgets($handle, 4096);
            fclose($handle);
        }

        if(isset($result)){
            $allData = explode(',', $result); /* Get all the contents to an array */
            $rate = $allData[1];
        }
        else{
            $rate = 0;
        }
        
        return($rate);
    }

    /**
     * Convertion function call to yahoo finance api
     * 
     * @param string $dataSource which dataSOurce need to use
     * @return boolean if the table standard currency_converters exist into the database
     */
    private function checkIfExistTable($dataSource){
        $autoIncrement = 'AUTO_INCREMENT';

        $db = ConnectionManager::get($dataSource);
        $config = $db->config();
        if (strpos($config['dsn'], 'sqlite') !== false) {
            $autoIncrement = 'AUTOINCREMENT';
        }

        $sql = 'CREATE TABLE IF NOT EXISTS `currency_converters` (
          `id` integer PRIMARY KEY ' . $autoIncrement . ',
          `fromCurrency` varchar(5) NOT NULL,
          `toCurrency` varchar(5) NOT NULL,
          `rates` varchar(10) NOT NULL,
          `created` datetime NOT NULL,
          `modified` datetime NOT NULL
        );';

        $results = $db->query($sql);
        return $results;
    }
}
