<?php
App::uses('Controller', 'Controller');
App::uses('Model', 'Model');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('CurrencyConverterComponent', 'CurrencyConverter.Controller/Component');

namespace CurrencyConverter\Test\TestCase\Controller\Component\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use CurrencyConverter\Test\TestCase\Controller\Component\CurrencyConverterComponent;

class TestConverterController extends Controller {

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
        $dataSource     = 'test';

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);

        $this->assertGreaterThan($result, $amount);
    }

    public function testAmountWithPoint() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20.00';
        $saveIntoDb     = 0;
        $hourDifference = 0;
        $dataSource     = 'test';

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);

        $this->assertGreaterThan($result, $amount);
    }

    public function testAmountWithCommaSavedInDatabase() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20,00';
        $saveIntoDb     = 1;
        $hourDifference = 1;
        $dataSource     = 'test';

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);

        $this->assertGreaterThan($result, $amount);
    }

    public function testAmountWithPointSavedInDatabase() {
        $fromCurrency   = 'EUR';
        $toCurrency     = 'GBP';
        $amount         = '20.00';
        $saveIntoDb     = 1;
        $hourDifference = 1;
        $dataSource     = 'test';

        $result = $this->CurrencyConverter->convert($fromCurrency, $toCurrency, $amount, $saveIntoDb, $hourDifference, $dataSource);

        $this->assertGreaterThan($result, $amount);
    }
}