<?php
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('CurrencyConverterComponent', 'CurrencyConverter.Controller/Component');

class TestConverterController extends Controller {
    // empty
}

class CurrencyConverterComponentTest extends CakeTestCase {

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
}