# CurrencyConverter component

Convert price or simply get currency rate from currency to another one.

## Adding the component

In the initialize method of your Controller load the component:

```php
$this->loadComponent('CurrencyConverter.CurrencyConverter', $config);
```

## Setting

By default component will use this following settings:

```php
$config = [
	'database' => true, // (true|false) precise if component have to store currency rate in database
	'refresh' => 24, // (int) precise interval time for refrshing currency rate in database, in case you are using database
	'decimal' => 2 // (int) number of decimal to use for the converted price
	'round' => false // (false|int) number to divise 1 and get the sup step to round price to (eg: 4 for 0.25 step)
];
```

- database (mention if component have to store currency rate in database)
- refresh (time interval for refreshing currency rate in database)
- decimal (number of decimal to use when formatting price)
- round (number to divise 1 and get the sup step to round price to eg: 4 for 0.25 step)

You can change default setting and write your own.

## Database

If you want to use database to store currency rate DO NOT FORGET to create the table wich will store it.
SQL schema is provided in table.txt file in config folder. Note that when using database, The Plugin store rate in session to avoid requesting database at each time we want to use the component. Do not be worry even rate stored in session the Plugin keep refresh rate when it has to do.

## Convert price

You can convert price from a currency to another one in your Controller. To do it you have to use convert method of the Component. Assuming you want to convert a price from EUR to GBP:

```php
$price_converted = $this->CurrencyConverter->convert($price_to_convert, $from, $to);
```

* **price_to_convert:** is the price to convert (Example: 10.5, '10.5' or '10,5').
* **from:** is the actual price currency (Example: EUR, GBP)
* **to:** is the currency that you want to convert your price to (Example: EUR, GBP)

To get a available list of currency code you can check here [List of available currency code](https://free.currencyconverterapi.com/api/v6/currencies)

Note: If plugin can't reach rate out from API the converted price will be 0.

## Get currency rate

You can get a currency rate from a currency to another one in your Controller. To do it you have to use rate method of the Component. Assuming you want to get the rate from EUR currency to GBP currency:

```php
$price_converted = $this->CurrencyConverter->rate($from, $to);
```

* **from:** is the actual price currency (Example: EUR, GBP)
* **to:** is the currency that you want to convert your price to (Example: EUR, GBP)

To get a available list of currency code you can check here [List of available currency code](https://free.currencyconverterapi.com/api/v6/currencies)

Note: If plugin can't reach rate out from API the rate, rate will be null.


