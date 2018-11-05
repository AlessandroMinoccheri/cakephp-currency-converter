<?php
namespace CurrencyConverter\Test\TestCase\View\Helper;

use Cake\Controller\Controller;
use Cake\Network\Request;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use CurrencyConverter\View\Helper\CurrencyConverterHelper;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

/**
 *
 *
 * BEFORE TESTTING MAKE SURE
 * TO WRITE CURRENT DATETIME INTO THE FIRST RECORDS OF CurrencyratesFixture in tests/Fixture/CurrencyratesFixture
 *
 *
 */
class CurrencyConverterHelperTest extends TestCase {

	public $fixtures = ['plugin.CurrencyConverter.Currencyrates'];

	/**
	 * Helper being tested
	 *
	 * @var \Ratings\View\Helper\CurrencyConverter
	 */
	public $CurrencyConverter;

	/**
	 * @var \Cake\Http\ServerRequest
	 */
	protected $request;

	/**
	 * @var \Cake\View\View
	 */
	protected $View;

	/**
	 * @var \Cake\ORM\Table
	 */
	protected $Table;

	/**
	 * (non-PHPdoc)
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->Request = new Request();
		$this->Controller = new Controller();
		$this->View = new View($this->Request);
		$this->CurrencyConverter = new CurrencyConverterHelper($this->View);

		$table = TableRegistry::get('Currencyrates');
        $this->Table = $table;
	}

	public function testConfig()
    {
        $this->CurrencyConverter = new CurrencyConverterHelper($this->View, []);
        $expected = [
            'database' => 2,
            'refresh' => 24,
            'decimal' => 2,
            'round' => false
        ];
        $this->assertEquals($expected, $this->CurrencyConverter->getConfig());
    }

    public function testConvertSameCurrency()
    {
        $amount = 20.00;
        $fromCurrency = 'EUR';
        $toCurrency = 'EUR';

        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $expected = 20.00;
        $this->assertEquals($expected, $result);
    }

    public function testConvertWithComma()
    {
        $amount = '20.00';
        $fromCurrency = 'EUR';
        $toCurrency = 'EUR';

        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $expected = 20.00;
        $this->assertEquals($expected, $result);
    }

    public function testConvertNumberFormatting()
    {
        $amount = 20.123456;
        $fromCurrency = 'EUR';
        $toCurrency = 'EUR';

        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $expected = 20.12;
        $this->assertEquals($expected, $result);

        $amount = 20.123456;
        $fromCurrency = 'EUR';
        $toCurrency = 'EUR';

        $this->CurrencyConverter = new CurrencyConverterHelper($this->View, [
            'decimal' => 3
        ]);
        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $expected = 20.123;
        $this->assertEquals($expected, $result);

        $this->CurrencyConverter = new CurrencyConverterHelper($this->View, [
            'round' => 0
        ]);
        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $expected = 20.12;
        $this->assertEquals($expected, $result);

        $this->CurrencyConverter = new CurrencyConverterHelper($this->View, [
            'round' => 4
        ]);
        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $expected = 20.25;
        $this->assertEquals($expected, $result);

        $this->CurrencyConverter = new CurrencyConverterHelper($this->View, [
            'round' => 1
        ]);
        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $expected = 21;
        $this->assertEquals($expected, $result);

        $amount = 20.00;
        $fromCurrency = 'EUR';
        $toCurrency = 'EUR';
        $this->CurrencyConverter = new CurrencyConverterHelper($this->View, [
            'round' => 4
        ]);
        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $expected = 20.00;
        $this->assertEquals($expected, $result);

        $amount = 20.88;
        $fromCurrency = 'EUR';
        $toCurrency = 'EUR';
        $this->CurrencyConverter = new CurrencyConverterHelper($this->View, [
            'round' => 4
        ]);
        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $expected = 21.00;
        $this->assertEquals($expected, $result);
    }

    public function testConvertUsingDatabaseWhenRateDoNotExistInDatabase()
    {
        $amount = 20.00;
        $fromCurrency = 'EUR';
        $toCurrency = 'USD';

        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $rate = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'USD'])->first()->rate;
        $expected = round(number_format($rate * 20.00, 2), 2);
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-USD')['rate'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'USD'])->first()->rate;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-USD')['modified'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'USD'])->first()->get('modified')->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->assertEquals($expected, $result);
    }

    public function testConvertUsingDatabaseWhenRateExistInDatabaseAndNoNeedToBeUpdated()
    {
        $amount = 20.00;
        $fromCurrency = 'EUR';
        $toCurrency = 'GBP';

        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $expected = number_format(0.8 * 20.00, 2);
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP')['rate'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->rate;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP')['modified'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->get('modified')->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->assertEquals($expected, $result);
    }

    public function testConvertUsingDatabaseWhenRateExistInDatabaseAndNeedToBeUpdated()
    {
        $amount = 20.00;
        $fromCurrency = 'EUR';
        $toCurrency = 'GBP';

        $this->CurrencyConverter = new CurrencyConverterHelper($this->View, [
            'refresh' => 0
        ]);
        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $rate = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->rate;
        $expected = round(number_format($rate * 20.00, 2), 2);
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP')['rate'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->rate;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP')['modified'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->assertEquals($expected, $result);
    }

    public function testConvertUsingDatabaseWhenRateExistInSession()
    {
        $amount = 20.00;
        $fromCurrency = 'EUR';
        $toCurrency = 'GBP';

        $now = Time::now()->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->Request->getSession()->write('CurrencyConverter.EUR-GBP', [
            'rate' => 0.15,
            'modified' => $now
        ]);

        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $expected = number_format(0.15 * 20.00, 2);
        $this->assertEquals($expected, $result);
    }

    public function testConvertUsingDatabaseWhenRateExistInSessionAndNeedToBeUpdated()
    {
        $amount = 20.00;
        $fromCurrency = 'EUR';
        $toCurrency = 'GBP';

        $expiredDatetime = Time::now()->modify('-5 days')->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->Request->getSession()->write('CurrencyConverter.EUR-GBP', [
            'rate' => 0.1,
            'modified' => $expiredDatetime
        ]);

        $entity = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first();
        $entity->set('modified', $expiredDatetime);
        $this->Table->save($entity);

        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);
        $rate = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->rate;
        $expected = number_format($rate * 20.00, 2);
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP.rate');
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->rate;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP.modified');
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->assertEquals($expected, $result);
    }

    public function testConvertNotUsingDatabse()
    {
        $amount = 20.00;
        $fromCurrency = 'GBP';
        $toCurrency = 'EUR';

        $this->CurrencyConverter = new CurrencyConverterHelper($this->View, [
            'database' => false
        ]);
        $result = $this->CurrencyConverter->convert($amount, $fromCurrency, $toCurrency);

        $this->assertGreaterThan(20, $result);

        $result = count($this->Table->find('all')->toArray());
        $this->assertEquals(1, $result);
    }


    public function testRateUsingDatabaseWhenRateDoNotExistInDatabase()
    {
        $fromCurrency = 'EUR';
        $toCurrency = 'USD';

        $result = $this->CurrencyConverter->rate($fromCurrency, $toCurrency);
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'USD'])->first()->rate;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-USD')['rate'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'USD'])->first()->rate;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-USD')['modified'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'USD'])->first()->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->assertEquals($expected, $result);
    }

    public function testRateUsingDatabaseWhenRateExistInDatabaseAndNoNeedToBeUpdated()
    {
        $fromCurrency = 'EUR';
        $toCurrency = 'GBP';

        $result = $this->CurrencyConverter->rate($fromCurrency, $toCurrency);
        $expected = 0.8;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP')['rate'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->rate;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP')['modified'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->get('modified')->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->assertEquals($expected, $result);
    }

    public function testRateUsingDatabaseWhenRateExistInDatabaseAndNeedToBeUpdated()
    {
        $fromCurrency = 'EUR';
        $toCurrency = 'GBP';

        $this->CurrencyConverter = new CurrencyConverterHelper($this->View, [
            'refresh' => 0
        ]);
        $result = $this->CurrencyConverter->rate($fromCurrency, $toCurrency);
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->rate;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP')['rate'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->rate;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP')['modified'];
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->assertEquals($expected, $result);
    }

    public function testRatetUsingDatabaseWhenRateExistInSession()
    {
        $amount = 20.00;
        $fromCurrency = 'EUR';
        $toCurrency = 'GBP';

        $now = Time::now()->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->Request->getSession()->write('CurrencyConverter.EUR-GBP', [
            'rate' => 0.15,
            'modified' => $now
        ]);

        $result = $this->CurrencyConverter->rate($fromCurrency, $toCurrency);
        $expected = 0.15;
        $this->assertEquals($expected, $result);
    }

    public function testRateUsingDatabaseWhenRateExistInSessionAndNeedToBeUpdated()
    {
        $amount = 20.00;
        $fromCurrency = 'EUR';
        $toCurrency = 'GBP';

        $expired = Time::now()->modify('-5 days')->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->Request->getSession()->write('CurrencyConverter.EUR-GBP', [
            'rate' => 0.1,
            'modified' => $expired
        ]);

        $entity = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first();
        $entity->set('modified', $expired);
        $this->Table->save($entity);

        $result = $this->CurrencyConverter->rate($fromCurrency, $toCurrency);
        $rate = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->rate;
        $expected = $rate;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP.rate');
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->rate;
        $this->assertEquals($expected, $result);

        $result = $this->Request->getSession()->read('CurrencyConverter.EUR-GBP.modified');
        $expected = $this->Table->find('all')->where(['from_currency' => 'EUR', 'to_currency' => 'GBP'])->first()->modified->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->assertEquals($expected, $result);
    }

    public function testRateNotUsingDatabse()
    {
        $fromCurrency = 'GBP';
        $toCurrency = 'EUR';

        $this->CurrencyConverter = new CurrencyConverterHelper($this->View, [
            'database' => false
        ]);
        $result = $this->CurrencyConverter->rate($fromCurrency, $toCurrency);

        $this->assertGreaterThan(1, $result);

        $result = count($this->Table->find('all')->toArray());
        $this->assertEquals(1, $result);
    }

    public function tearDown()
    {
        parent::tearDown();
        // Nettoie la Table
        TableRegistry::clear();
        // Nettoie les variables quand les tests sont finis.
		unset($this->CurrencyConverter);
    }
}
