<?php
App::uses('Controller', 'Controller');
App::uses('Model', 'Model');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('CurrencyConverterComponent', 'CurrencyConverter.Controller/Component');

class TestConverterController extends Controller {
    // empty
}

class CurrencyConverterComponentTest extends CakeTestCase {
    public $fixtures = array('plugin.CurrencyConverter.CurrencyConverter');
    public $CurrencyConverterComponent = null;
    public $Controller = null;

    public function setUp() {
        parent::setUp();
        $Collection = new ComponentCollection();
        $this->CurrencyConverter = new CurrencyConverterComponent($Collection);
        $CakeRequest = new CakeRequest();
        $CakeResponse = new CakeResponse();
        $this->Controller = new TestConverterController($CakeRequest, $CakeResponse);
    }

    public function tearDown() {
        parent::tearDown();
        unset($this->CurrencyConverterComponent);
    }

    public function testAmountWithComma() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20,00';
        $saveIntoDb     = 0;
        $hourDifference = 0;

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference);

        $this->assertGreaterThan($result, $amount);
    }

    public function testAmountWithPoint() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20.00';
        $saveIntoDb     = 0;
        $hourDifference = 0;

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference);

        $this->assertGreaterThan($result, $amount);
    }

    public function testAmountWithCommaSavedInDatabase() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20,00';
        $saveIntoDb     = 1;
        $hourDifference = 1;

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference);

        $this->assertGreaterThan($result, $amount);
    }

    public function testAmountWithPointSavedInDatabase() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20.00';
        $saveIntoDb     = 1;
        $hourDifference = 1;

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference);

        $this->assertGreaterThan($result, $amount);
    }

    public function testCheckInsertedInDatabase() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $hourDifference = 1;
        $expected = 1;

        $result = $this->CurrencyConverter->checkToFind($fromCurrency, $toCurrency, $hourDifference);

        $this->assertEquals($expected, $result['find']);
        $this->assertGreaterThan(0, $result['rate']);
    }
}