<?php
namespace App\Test\TestCase\Controller\Component;

use CurrencyConverter\Controller\Component\CurrencyConverterComponent;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

class CurrencyConverterComponentTest extends TestCase {
    public $fixtures = ['app.currencyconverter'];
    public $CurrencyConverter = null;
    public $controller = null;

    public function setUp()
    {
        parent::setUp();

        $this->currencyConverter = new CurrencyConverterComponent(new ComponentRegistry(new Controller));
    }

    public function testAmountWithComma()
    {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20,00';
        $saveIntoDb     = 0;
        $hourDifference = 0;
        $dataSource     = 'test';

        $result = $this->currencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);
        $this->assertGreaterThan((float)$result, (float)$amount);
    }

    public function testAmountWithPoint()
    {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20.00';
        $saveIntoDb     = 0;
        $hourDifference = 0;
        $dataSource     = 'test';

        $result = $this->currencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);
        $this->assertGreaterThan($result, $amount);
    }

    public function testAmountWithCommaSavedInDatabase()
    {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20,00';
        $saveIntoDb     = true;
        $hourDifference = 1;
        $dataSource     = 'test';

        $result = $this->currencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);
        $this->assertGreaterThan($result, $amount);
    }

    public function testAmountWithPointSavedInDatabase()
    {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20.00';
        $saveIntoDb     = 1;
        $hourDifference = 1;
        $dataSource     = 'test';

        $result = $this->currencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);
        $this->assertGreaterThan($result, $amount);
    }

    public function testConvertToPDS()
    {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'PDS';
        $amount         = '20.00';
        $saveIntoDb     = 1;
        $hourDifference = 1;
        $dataSource     = 'test';

        $result = $this->currencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);
        $this->assertGreaterThan($result, $amount);
    }

    public function testConvertFromPDS()
    {
        $fromCurrency   = 'PDS';
        $toCurrency     = 'EUR';
        $amount         = '20.00';
        $saveIntoDb     = 1;
        $hourDifference = 1;
        $dataSource     = 'test';

        $result = $this->currencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);
        $this->assertGreaterThan($amount, $result);
    }

    public function testConvertSameCurrency()
    {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'EUR';
        $amount         = '20.00';
        $saveIntoDb     = 1;
        $hourDifference = 1;
        $dataSource     = 'test';

        $result = $this->currencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);
        $this->assertEquals($result, $amount);
    }

    public function testInsertCurrency()
    {
        $currencyTable = TableRegistry::get('CurrencyConverter', [
            'className' => 'CurrencyConverter\Model\Table\CurrencyConvertersTable',
            'table' => 'currency_converter'
        ]);

        $query = $currencyTable->find('all');

        $query->hydrate(false);
        $result =  $query->toArray();

        foreach ($result as $row){
            $currencyTable->deleteAll(['id' => $row['id']]);
        }

        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20.00';
        $saveIntoDb     = 1;
        $hourDifference = -1;
        $dataSource     = 'test';

        $resultConverted = $this->currencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);

        $currencyTable = TableRegistry::get('CurrencyConverter');
        $query = $currencyTable->find('all');

        $query->hydrate(false);
        $result =  $query->toArray();

        $this->assertEquals(1, count($result));
        $this->assertGreaterThan($resultConverted, $amount);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->currencyConverter, $this->controller);
    }
}