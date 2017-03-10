![Php Instapago](help/hYNsH6B.png)
<p align="center">
    Librería Instapago para PHP
</p>
<p align="center">
    <sup style="color: #d0d0d0;"><b>NOTA</b> Los logos son propiedad de Instapago y Banesco, respectivamente.</sup>
</p>
[![GitHub issues](https://img.shields.io/github/issues/abr4xas/php-instapago.svg?style=flat-square)](https://github.com/abr4xas/php-instapago/issues) [![GitHub forks](https://img.shields.io/github/forks/abr4xas/php-instapago.svg?style=flat-square)](https://github.com/abr4xas/php-instapago/network) [![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://raw.githubusercontent.com/abr4xas/php-instapago/master/LICENSE)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/abr4xas/php-instapago/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/abr4xas/php-instapago/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/a2b75f8ad49b4a988400fc4633737f28)](https://www.codacy.com/app/abr4xas/php-instapago?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=abr4xas/php-instapago&amp;utm_campaign=Badge_Grade)
[![Join the chat at https://gitter.im/abr4xas/php-instapago](https://badges.gitter.im/abr4xas/php-instapago.svg)](https://gitter.im/abr4xas/php-instapago?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://travis-ci.org/abr4xas/php-instapago.svg?branch=master)](https://travis-ci.org/abr4xas/php-instapago)

## instalación

Primero, [`composer`](https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md)

Luego:

```
$ composer require instapago/instapago
$ composer dumpautoload -o // opcional
```

### como usar

creamos un archivo `index.php`


#### composer

```php
<?php

require 'vendor/autoload.php';

use \Instapago\Api;


$paymentData = [
  'amount' => '200',
  'description' => 'test',
  'card_holder' => 'jon doe',
  'card_holder_id' => '11111111',
  'card_number' => '4111111111111111',
  'cvc' => '123',
  'expiration' => '12/2020',
  'ip' => '127.0.0.1',
];

try{

  $api = new Api('<keyId>','<publicKeyId>');

  $respuesta = $api->directPayment($paymentData);
  // hacer algo con $respuesta
}catch(\Instapago\Exceptions\InstapagoException $e){

  echo $e->getMessage(); // manejar el error

}
```

Podemos revisar rápidamente si todo funciona correctamente escribiendo:

```bash
$ php -S localhost:8000
```

### llaves de pruebas

```
* keyId = 74D4A278-C3F8-4D7A-9894-FA0571D7E023
* publicKeyId = e9a5893e047b645fed12c82db877e05a
```

## phpunit

```
$ phpunit --configuration="phpunit.xml.dist"
```

## enlaces

* [Documentación de la librería](help/DOCUMENTACION.md)
* [Registro de cambios](CHANGELOG.md)
* [Colaboración](help/CONTRIBUCION.md)
* [Autores](help/AUTORES.md)

## licencia

Licencia [MIT](http://opensource.org/licenses/MIT) :copyright: 2016
