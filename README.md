cakephp-currency-converter V 1.1.4
==============================

[![Latest Stable Version](https://poser.pugx.org/alessandrominoccheri/cakephp-currency-converter/v/stable.svg)](https://packagist.org/packages/alessandrominoccheri/cakephp-currency-converter)
[![License](https://poser.pugx.org/alessandrominoccheri/cakephp-currency-converter/license.svg)](https://packagist.org/packages/alessandrominoccheri/cakephp-currency-converter)
[![Build Status](https://api.travis-ci.org/AlessandroMinoccheri/cakephp-currency-converter.png)](https://travis-ci.org/AlessandroMinoccheri/cakephp-currency-converter)
[![Total Downloads](https://poser.pugx.org/alessandrominoccheri/cakephp-currency-converter/d/total.png)](https://packagist.org/packages/alessandrominoccheri/cakephp-currency-converter)

A CakePHP plugin to convert your price from a currency to another

---

##Background

Is very frequently that inside your sites you need to convert your price from a currency to another.
This library convert your price in every currency of the world.

It works with yahoo finance api and store currency rates inside the site database if you want.
User can configure in hour the time of currency rates update, if the user doesn't want to use database, rates are updated every time with the current conversion.

If you have set to use database, for example, user sets to update currency rates every hour, this library get the currency conversion from yahoo finance the first time, store it inside the database and for the next hour it takes conversion rates from the database if exist.
In this way reduce the request time to convert and every hour currency rates are updated.

If you haven't set to use database, instead, every time you call the library it makes a request to yahoo api and gets the actual conversion rate. This solution is great if you haven't a lot of request. Instead, if you have a lot of request  conversion is better to use the database configuration.

---

##Requirements

* CakePHP 2.x
* PHP 5.x 

---

#Installation
To install the plugin inside your cakephp project you can do this:

_[GIT Submodule]_

* Open your console 
* Go inside the folder you have got
* Launch the command: 
```
git submodule add -f https://github.com/AlessandroMinoccheri/cakephp-currency-converter.git
app/Plugin/CurrencyConverter/.
```

_[Manual]_

* Download this: [https://github.com/AlessandroMinoccheri/cakephp-currency-converter/archive/master.zip](https://github.com/AlessandroMinoccheri/cakephp-currency-converter/archive/master.zip)
* Unzip that download.
* Copy the resulting folder to `app/Plugin`
* Rename the folder you just copied to `CurrencyConverter`

_[GIT Clone]_

In your `Plugin` directory type:

    git clone https://github.com/AlessandroMinoccheri/cakephp-currency-converter.git CurrencyConverter
    
---

##Enable plugin

In cakephp 2.x you need to enable the plugin your app/Config/bootstrap.php file:
```
CakePlugin::load('CurrencyConverter');
```

If you are already using CakePlugin::loadAll();, then this is not necessary.

---
#Usage
To convert your price form your controller you need to load the plugin (view section Enable plugin) and type something like this:
```
$price = $this->CurrencyConverter->convert('GBP', 'EUR', '2100,00', 1, 1);
```

With this call you have inside variable "price" the new price converter from GBP to EUR.

To get a list of currency code you can check here:

[List of available currency code](http://www.xe.com/iso4217.php )

To view an example of usage you can clone this repository that I have created with this plugin installed inside and already works:
[Example](https://github.com/AlessandroMinoccheri/cakephp-currency-converter-example)

If you have some kind of problem like this issue:
https://github.com/AlessandroMinoccheri/cakephp-currency-converter/issues/1#issuecomment-42709382

Try to give 777 to the plugin folder

---
#Params

The function declaration to retrieve your converted price is:
```
function convert($from_currency, $to_currency, $amount, $save_into_db = 1, $hour_difference = 1)
```

* from_currency: is the actual price currency (Example: EUR, GBP)
* to_currency: is the currency that you want to convert your price (Example: EUR, GBP)
* amount: is the price to convert (Example: 200,20)
* save_into_db: is the variable that configure to use the database or not, if not hour_difference params is escaped
* hour_difference: is the hour difference to update your currency conversion. For example if you have set to update currency rates every hour, this library get the currency conversion from yahoo finance the first time, store it inside the database and for the next hour it takes conversion rates from the database if exist.

---

##Coming Soon

In coming the plugin version for cakephp 3.0


---
##License

The MIT License (MIT)

Copyright (c) 2014 Alessandro Minoccheri

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
