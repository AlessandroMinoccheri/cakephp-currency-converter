# Installation

Installing the Plugin is pretty much as with every other CakePHP Plugin.

## How to include

```
composer require "alessandrominoccheri/cakephp-currency-converter"
```
Details @ https://packagist.org/packages/alessandrominoccheri/cakephp-currency-converter

## Enable plugin

If you haven't use composer to include the Plugin in your App make sure the plugin is enable in your config/bootstrap.php file:

```
Plugin::load('CurrencyConverter');
```

If you are already using Plugin::loadAll();, then this is not necessary.


