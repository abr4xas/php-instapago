![Php Instapago](help/hYNsH6B.png)
<p align="center">
    Librería Instapago para PHP
</p>
<p align="center">
    <sup style="color: #d0d0d0;"><b>NOTA</b> Los logos son propiedad de Instapago y Banesco, respectivamente.</sup>
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/instapago/instapago.svg?style=flat-square)](https://packagist.org/packages/instapago/instapago)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/instapago/instapago/run-tests?label=tests)](https://github.com/instapago/instapago/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/instapago/instapago.svg?style=flat-square)](https://packagist.org/packages/instapago/instapago)


## instalación

Primero, [`composer`](https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md)

Luego:

```
$ composer require instapago/instapago
$ composer dumpautoload -o // opcional
```

### como usar

creamos un archivo `index.php`

```php
<?php

require 'vendor/autoload.php';

use \Instapago\Instapago\Api;


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

}catch(\Instapago\Exceptions\AuthException $e){

  echo $e->getMessage(); // manejar el error

}catch(\Instapago\Exceptions\BankRejectException $e){

  echo $e->getMessage(); // manejar el error

}catch(\Instapago\Exceptions\InvalidInputException $e){

  echo $e->getMessage(); // manejar el error

}catch(\Instapago\Exceptions\TimeoutException $e){

  echo $e->getMessage(); // manejar el error

}catch(\Instapago\Exceptions\ValidationException $e){

  echo $e->getMessage(); // manejar el error

}
```

Podemos revisar rápidamente si todo funciona correctamente escribiendo:

```bash
$ php -S localhost:8000
```

## tests


```
$ composer all
```

## enlaces

* [Documentación de la librería](help/DOCUMENTACION.md)
* [Registro de cambios](CHANGELOG.md)
* [Colaboración](help/CONTRIBUCION.md)
* [Autores](help/AUTORES.md)

## licencia

Licencia [MIT](http://opensource.org/licenses/MIT) :copyright: 2016
