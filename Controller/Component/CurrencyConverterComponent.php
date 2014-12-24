<?php
App::uses('Component', 'Controller', 'Session');

class CurrencyConverterComponent extends Component {
    var $controller = '';
    var $components = array('RequestHandler');

    /**
     * Initialization to get controller variable
     *
     * @param Controller $controller The controller to use.
     * @param array $settings Array of settings.
     */
    function initialize(Controller $controller, $settings = array()) { 
        $this->controller =& $controller; 
    } 

    /**
     * Convertion function
     *
     * @param string $from_currency the starting currency that user wants to convert to.
     * @param string $to_currency the ending currency that user wants to convert to.
     * @param float $amount the amount to convert.
     * @param boolean $save_into_db if develop wants to store convertion rate for use it without resending data to yahoo service.
     * @param int $hour_difference the hour difference to check if the last convertion is passed, if yes make a new call to yahoo finance api.
     * @return float the total amount converted into the new currency
     */
    public function convert($from_currency, $to_currency, $amount, $save_into_db = 1, $hour_difference = 1) {
        if($from_currency != $to_currency){
            $find = 0;
            $rate = 0;

            if ($from_currency=="PDS")
                $from_currency = "GBP";
            
            if($save_into_db == 1){
                $this->_checkIfExistTable();

                $CurrencyConverter = ClassRegistry::init('CurrencyConverter');
                $result = $CurrencyConverter->find('all', array('conditions' => 
                    array('from' => $from_currency, 'to' => $to_currency)));

                foreach ($result as $row){
                    $find = 1;
                    $last_updated = $row['CurrencyConverter']['modified'];
                    $now = date('Y-m-d H:i:s');
                    $d_start = new DateTime($now);
                    $d_end = new DateTime($last_updated);
                    $diff = $d_start->diff($d_end);

                    if(((int)$diff->y >= 1) || ((int)$diff->m >= 1) || ((int)$diff->d >= 1) || ((int)$diff->h >= $hour_difference) || ((double)$row['CurrencyConverter']['rates'] == 0)){
                        $rate = $this->_getRates($from_currency, $to_currency);

                        $CurrencyConverter->id = $row['CurrencyConverter']['id'];
                        $CurrencyConverter->set(array(
                            'from' => $from_currency,
                            'to' => $to_currency,
                            'rates' => $rate,
                            'modified' => date('Y-m-d H:i:s'),
                        ));
                        $CurrencyConverter->save();
                    }
                    else{
                        $rate = $row['CurrencyConverter']['rates'];
                    }
                }

                if($find == 0){
                    $rate = $this->_getRates($from_currency, $to_currency);

                    $CurrencyConverter->create();
                    $CurrencyConverter->set(array(
                        'from' => $from_currency,
                        'to' => $to_currency,
                        'rates' => $rate,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
                    ));
                    $CurrencyConverter->save();
                }
                $value = (double)$rate*(double)$amount;
                return number_format((double)$value, 2, '.', '');
            }
            else{
                $rate = $this->_getRates($from_currency, $to_currency);
                $value = (double)$rate*(double)$amount;
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
     * @param string $from_currency the starting currency that user wants to convert to.
     * @param string $to_currency the ending currency that user wants to convert to.
     * @return float the rate of convertion
     */
    private function __getRates($from_currency, $to_currency){
        $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $from_currency . $to_currency .'=X';
        $handle = @fopen($url, 'r');
         
        if ($handle) {
            $result = fgets($handle, 4096);
            fclose($handle);
        }

        if(isset($result)){
            $allData = explode(',',$result); /* Get all the contents to an array */
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
     * @return boolean if the table standard currency_converters exist into the database
     */
    private function _checkIfExistTable(){
        $find = 0;
        
        App::uses('ConnectionManager', 'Model');
        $db = ConnectionManager::getDataSource('default');
        $tables = $db->listSources();
        foreach($tables as $t){
            if($t == 'currency_converters'){
                $find = 1;
            }
        }

        if($find == 0){
            $sql = 'CREATE TABLE IF NOT EXISTS `currency_converters` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `from` varchar(5) NOT NULL,
              `to` varchar(5) NOT NULL,
              `rates` varchar(10) NOT NULL,
              `created` datetime NOT NULL,
              `modified` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';

            $results = $db->query($sql);
        }
        else
            return(true);
    }
}