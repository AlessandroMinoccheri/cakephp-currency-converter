<?php

namespace CurrencyConverter\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Database\Schema\TableSchema;

class CurrencyConverterComponent extends Component
{
    public $controller = null;

    private $fromCurrency;

    private $toCurrency;

    private $amount;

    private $hourDifference;

    private $saveIntoDb;

    private $dataSource;

    private $rate;

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
    public function convert(
        $fromCurrency, 
        $toCurrency, 
        $amount, 
        $saveIntoDb = true, 
        $hourDifference = 1, 
        $dataSource = 'default'
    ) {
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency = $toCurrency;
        $this->amount = $amount;
        $this->saveIntoDb = $saveIntoDb;
        $this->hourDifference = $hourDifference;
        $this->dataSource = $dataSource;
        $this->rate = 0;

        if($this->fromCurrency != $this->toCurrency){
            if ($this->fromCurrency == "PDS"){
                $this->fromCurrency = "GBP";
            }
            
            if($this->saveIntoDb == 1){
                $this->ensureIfExistTable();

                $CurrencyConverter = TableRegistry::get('CurrencyConverter', [
                    'className' => 'CurrencyConverter\Model\Table\CurrencyConvertersTable',
                    'table' => 'currency_converter'
                ]);
                
                $arrReturn = $this->updateDatabaseIfNecessary();
                
                if(isset($arrReturn['find'])){
                    $find = $arrReturn['find'];
                }

                if(isset($arrReturn['rate'])){
                    $this->rate = $arrReturn['rate'];
                }

                if($find == 0){
                    $this->rate = $this->getRates();

                    $data = [
                        'fromCurrency' => $this->fromCurrency,
                        'toCurrency'   => $this->toCurrency,
                        'rates'        => $this->rate,
                        'created'      => date('Y-m-d H:i:s'),
                        'modified'     => date('Y-m-d H:i:s'),
                    ];

                    $entity = $CurrencyConverter->newEntity($data);
                    $CurrencyConverter->save($entity);
                }

                $value = (double)$this->rate * (double)$this->amount;
                
                return number_format((double)$value, 2, '.', '');
            }
            else{
                $this->rate = $this->getRates($this->fromCurrency, $this->toCurrency);
                $value = (double)$this->rate * (double)$this->amount;
                
                return number_format((double)$value, 2, '.', '');
            }
        }
        
        return number_format((double)$this->amount, 2, '.', '');
    }

    /**
     * Convertion function call to yahoo finance api
     *
     * @param string $fromCurrency the starting currency that user wants to convert to.
     * @param string $toCurrency the ending currency that user wants to convert to.
     * @param int $hourDifference the hour difference to check if the last convertion is passed, if yes make a new call to yahoo finance api.
     * @return int if it's finded value
     */
    public function updateDatabaseIfNecessary() {
        $arrReturn = array();
        $find = 0;
        $rate = 0;

        $currencyTable = TableRegistry::get('CurrencyConverter', [
            'className' => 'CurrencyConverter\Model\Table\CurrencyConvertersTable',
            'table' => 'currency_converter'
        ]);

        $query = $currencyTable->find('all')
            ->where(['fromCurrency' => $this->fromCurrency, 'toCurrency' => $this->toCurrency ]);

        $query->hydrate(false);
        $result =  $query->toArray();

        foreach ($result as $row){
            $find = 1;
            $lastUpdated = str_replace(',', '', $row['modified']);
            $lastUpdated = str_replace('(', '', $lastUpdated);
            $lastUpdated = str_replace(')', '', $lastUpdated);
            $lastUpdated = str_replace('/', '-', $lastUpdated);
            $now = date('Y-m-d H:i:s');
            $dStart = new \DateTime($now);
            $dEnd = new \DateTime(trim($lastUpdated, '"'));
            $diff = $dStart->diff($dEnd);

            if ($this->ensureNeedToUpdateDatabase($diff, $row)) {
                $this->rate = $this->getRates($this->fromCurrency, $this->toCurrency);

                $data = [
                    'fromCurrency'        => $this->fromCurrency,
                    'toCurrency'          => $this->toCurrency,
                    'rates'       => $this->rate,
                    'modified'    => date('Y-m-d H:i:s'),
                ];

                $entity = $currencyTable->get($row['id']); 
                $currencyTable->patchEntity($entity, $data);

                $currencyTable->save($entity);
            }
            else{
                $this->rate = $row['CurrencyConverter']['rates'];
            }
        }

        $arrReturn['find'] = $find;
        $arrReturn['rate'] = $this->rate;

        return $arrReturn;
    }

    private function ensureNeedToUpdateDatabase($diff, $row)
    {
        return (
            ((int)$diff->y >= 1) || 
            ((int)$diff->m >= 1) || 
            ((int)$diff->d >= 1) || 
            ((int)$diff->h >= $this->hourDifference) || 
            ((double)$row['CurrencyConverter']['rates'] == 0)
        );
    }

    /**
     * Convertion function call to yahoo finance api
     *
     * @param string $fromCurrency the starting currency that user wants to convert to.
     * @param string $toCurrency the ending currency that user wants to convert to.
     * @return float the rate of convertion
     */
    private function getRates(){
        $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $this->fromCurrency . $this->toCurrency .'=X';
        $handle = @fopen($url, 'r');
         
        if ($handle) {
            $result = fgets($handle, 4096);
            fclose($handle);
        }

        if(isset($result)){
            $allData = explode(',', $result); /* Get all the contents to an array */
            return $allData[1];
        }
        
        return $this->rate = 0;
    }

    /**
     * Convertion function call to yahoo finance api
     * 
     * @param string $dataSource which dataSOurce need to use
     * @return boolean if the table standard currency_converters exist into the database
     */
    private function ensureIfExistTable(){
        $autoIncrement = 'AUTO_INCREMENT';

        $db = ConnectionManager::get($this->dataSource);
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

        return $db->query($sql);
    }
}
