cakephp-currency-converter V 3.1.0
==============================

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/AlessandroMinoccheri/cakephp-currency-converter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AlessandroMinoccheri/cakephp-currency-converter/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/AlessandroMinoccheri/cakephp-currency-converter/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AlessandroMinoccheri/cakephp-currency-converter/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/alessandrominoccheri/cakephp-currency-converter/v/stable.svg)](https://packagist.org/packages/alessandrominoccheri/cakephp-currency-converter)
[![License](https://poser.pugx.org/alessandrominoccheri/cakephp-currency-converter/license.svg)](https://packagist.org/packages/alessandrominoccheri/cakephp-currency-converter)
[![Build Status](https://api.travis-ci.org/AlessandroMinoccheri/cakephp-currency-converter.png)](https://travis-ci.org/AlessandroMinoccheri/cakephp-currency-converter)
[![Total Downloads](https://poser.pugx.org/alessandrominoccheri/cakephp-currency-converter/d/total.png)](https://packagist.org/packages/alessandrominoccheri/cakephp-currency-converter)

A CakePHP 3.x Plugin to
- convert price from a currency to another one
- get rate from a currency to another one

---

## Background

Is very frequently that inside your sites you need to convert your price or simply get a currency rate from a currency to another one.
This Plugin convert your price or provide to you a rate from a currency to another one currency in every currency of the world.

It works with [free.currencyconverterapi.com](https://free.currencyconverterapi.com/) and can store currency rates inside a database if you want to. In case you want to use database to store currency rates inside, DO NOT FORGET to create the table to store rates into (sql schema is given to you in config/table.txt).

You can configure refresh key in configuration settings to get new conversion from free.currencyconverterapi.com, if you don't want to use database, rates are updated every time with the current conversion.

If you have set to use database, for example, you can set to update currency rates every hour, this library get the currency conversion from free.currencyconverterapi.com the first time, store it inside the database and for the next hour it takes conversion rates from the database if exist.
In this way reduce the request time to convert and every hour currency rates are updated.

If you haven't set to use database, instead, every time you call the library it makes a request to free.currencyconverterapi.com and gets the actual conversion rate. This solution is great if you haven't a lot of request. Instead, if you have a lot of request conversions is better to use the database configuration.

---

## Requirements

* CakePHP 3.x
* PHP >=5.6

For cakephp 2.x you can download version 1.1.4

---

## Installation & Docs

- [Documentation](docs/README.md)

---

## License

The MIT License (MIT)

Copyright (c) 2014 Alessandro Minoccheri

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

---

### TODOs

* Use a second one API to convert price (ex [fixer.io](https://fixer.io/)) and purpose wanted API to convert price in Component and Helper settings.
