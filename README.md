<p align="center">
<img src="help/hYNsH6B.png">

</p>
<p align="center">
    Librería Instapago para PHP
</p>
<p align="center">
    <sup style="color: #d0d0d0;"><b>NOTA</b> Los logos son propiedad de Instapago y Banesco, respectivamente.</sup>
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/instapago/instapago.svg?style=flat-square)](https://packagist.org/packages/instapago/instapago)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/abr4xas/php-instapago/Tests?style=flat-square)](https://github.com/abr4xas/php-instapago/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/instapago/instapago.svg?style=flat-square)](https://packagist.org/packages/instapago/instapago)


## instalación

Primero, [`composer`](https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md)

Luego:

```
$ composer require instapago/instapago
$ composer dumpautoload -o // opcional
```

### como usar

>NOTA: Tomar en cuenta que esta nueva versión fue probada usando php7.4, no aseguro que funcione en algo menor a eso, en *teoría* debería pero no estoy seguro.

```php
<?php

require 'vendor/autoload.php';

use \Instapago\Instapago\Api;
use \Instapago\Instapago\Exceptions\{
	InstapagoException,
	AuthException,
	BankRejectException,
	InvalidInputException,
	TimeoutException,
	ValidationException,
	GenericException,
};


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
}catch(InstapagoException $e){

  echo $e->getMessage(); // manejar el error

}catch(AuthException $e){

  echo $e->getMessage(); // manejar el error

}catch(BankRejectException $e){

  echo $e->getMessage(); // manejar el error

}catch(InvalidInputException $e){

  echo $e->getMessage(); // manejar el error

}catch(TimeoutException $e){

  echo $e->getMessage(); // manejar el error

}catch(ValidationException $e){

  echo $e->getMessage(); // manejar el error

}catch(GenericException $e){

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
