![Php Instapago](hYNsH6B.png)
<p align="center">
Documentación de la librería <b>Instapago</b> - Versión Refactorizada
</p>

----

Table of Contents
=================
* [Información General](#información-general)
  * [Credenciales de Pruebas](#credenciales-de-pruebas)
  * [Requisitos del Sistema](#requisitos-del-sistema)
  * [Parámetros <em>requeridos</em> para crear un pago](#parámetros-requeridos-para-crear-un-pago)
  * [Retornos](#retornos)
  * [Manejo de errores](#manejo-de-errores)
  * [Códigos de respuesta](#códigos-de-respuesta)
  * [Tarjetas de prueba](#tarjetas-de-prueba)
* [API](#api)
  * [Instanciación](#instanciación)
  * [Configuración Avanzada](#configuración-avanzada)
  * [Crear un Pago Directo](#crear-un-pago-directo)
  * [Reservar un Pago](#reservar-un-pago)
  * [Completar Pago](#completar-pago)
  * [Información de un Pago](#información-de-un-pago)
  * [Anular Pago](#anular-pago)
* [Características Avanzadas](#características-avanzadas)
  * [DTOs (Data Transfer Objects)](#dtos-data-transfer-objects)
  * [Sistema de Validación](#sistema-de-validación)
  * [Logging](#logging)
  * [Testing](#testing)


## Información General

### Credenciales de Pruebas
```
* keyId = 74D4A278-C3F8-4D7A-9894-FA0571D7E023
* publicKeyId = e9a5893e047b645fed12c82db877e05a
```
> [!NOTE]  
> Debes solicitar las llaves públicas y privadas (`publicKeyId` y `keyId`) a Instapago. [Aquí](http://instapago.com) puedes encontrar mayor información.

> [!WARNING]  
> Esta implementación es funcional con las version 1.6 de instapago

### Requisitos del Sistema

Esta versión refactorizada requiere:

* **PHP 8.2 o superior**
* **Composer** para manejo de dependencias
* **Extensión cURL** o **Guzzle HTTP** para peticiones HTTP
* **Extensión JSON** para manejo de datos

#### Dependencias:
* `guzzlehttp/guzzle: ^7.0` - Cliente HTTP
* `pestphp/pest: ^3.7` - Framework de testing (desarrollo)

#### Características de PHP Utilizadas:
* Readonly classes
* Named arguments
* Constructor property promotion
* Enums
* Union types


### Parámetros _requeridos_ para crear un pago

* `card_holder`: Nombre del Tarjeta habiente.
* `card_holder_id`: Cédula del Tarjeta Habiente,
* `card_number`: Número de la tarjeta de crédito, 16 dígitos sin separadores.
* `cvc`: Código de validación de la Tarjeta de crédito.
* `expiration_date`: Fecha de Vencimiento de la tarjeta. Formato MM/YYYY. Por Ejemplo: 10/2015.
* `amount`: Monto a Debitar, formato: `0.00` (punto como separador decimal, sin separador de miles).
* `description`: Texto con la descripción de la operación.
* `ip`: Dirección IP del cliente.

De ahora en más usaremos `$paymentData` para referirnos a el arreglo con los parámetros antes mencionados.

```php
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
```

### Retornos

Todos los métodos del api devuelven un arreglo asociativo con el siguiente esquema:

* `code`: (numeric) Codigo de respuesta de Instapago.
* `msg_banco`: (string) Mensaje del banco respecto a la transacción.
* `voucher`: (string) Voucher (muy parecido al ticket que emite el POS en Venezuela) en html.
* `id_pago`: (string) Identificador del pago en la plataforma de Instapago.
* `reference`: (numeric) Referencia del pago en la red bancaria.
* `original_response`: (array) Respuesta original de la plataforma de instapago.

### Manejo de errores

La excepción base de la librería es `\Instapago\Exceptions\InstapagoException` y reporta errores generales con instapago, y de ella se derivan 5 excepciones de la siguiente manera.

* `Instapago\Exceptions\AuthException`: es lanzada cuando Instapago retorna error en las credenciales.
* `Instapago\Exceptions\BankRejectException`: es lanzada cuando un pago es rechazado por el banco.
* `Instapago\Exceptions\InvalidInputException`: es lanzada cuando instapago rechaza la entrada de datos.
* `Instapago\Exceptions\TimeoutException`: es lanzada cuando es imposible conectarse al api de Instapago y expira el tiempo de carga.
* `Instapago\Exceptions\ValidationException`: es lanzada cuando la entrada de datos es inválida. (antes de ser enviada a Instapago).

### Códigos de respuesta

Para todas las transacciones realizadas bajo el API de Instapago, los códigos HTTP de respuestas corresponden a los siguientes estados:

* ```201```: Pago procesado con éxito.
* ```400```: Error al validar los datos enviados (Adicionalmente se devuelve una cadena de caracteres con la descripción del error).
* ```401```: Error de autenticación, ha ocurrido un error con las llaves utilizadas.
* ```403```: Pago Rechazado por el banco.
* ```500```: Ha Ocurrido un error interno dentro del servidor.
* ```503```: Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo.

> **Importante**: Si recibe un código de respuesta diferente a los antes descritos deben ser tomados como errores de protocolo HTTP.

### Tarjetas de prueba

Para realizar las pruebas, se provee de los siguientes datos para comprobar la integración:

* Tarjetas Aprobadas:

Pueden indicar cualquier valor para Cédula o RIF, Fecha de Vencimiento y CVC:

* Visa: `4111111111111111`
* American Express: `378282246310005`
* MasterCard: `5105105105105100`
* Sambil: `8244001100110011`
* Rattan: `8244021100110011`
* Locatel: `8244041100110011`


## API

### Instanciación

#### Instanciación Básica

```php
use Instapago\Instapago\Api;

$api = new Api('<keyId>', '<publicKeyId>');
```

#### Instanciación con Dependencias Personalizadas

```php
use Instapago\Instapago\Api;
use Instapago\Instapago\Http\GuzzleHttpClientFactory;
use Instapago\Instapago\Config\InstapagoConfig;
use Instapago\Instapago\Logging\NullLogger;

// Configuración personalizada
$config = new InstapagoConfig(
    baseUri: 'https://api.instapago.com/',
    timeout: 30,
    debug: false
);

// Cliente HTTP personalizado
$httpClientFactory = new GuzzleHttpClientFactory($config);
$httpClient = $httpClientFactory->create();

// Logger personalizado
$logger = new NullLogger();

// API con dependencias inyectadas
$api = new Api('<keyId>', '<publicKeyId>', $httpClient, $logger);
```

### Configuración Avanzada

#### Clase InstapagoConfig

```php
use Instapago\Instapago\Config\InstapagoConfig;

// Configuración por defecto
$config = InstapagoConfig::default();

// Configuración con debug habilitado
$config = InstapagoConfig::withDebug();

// Configuración personalizada completa
$config = new InstapagoConfig(
    baseUri: 'https://api.instapago.com/',
    timeout: 60,
    debug: true,
    headers: [
        'User-Agent' => 'MiApp/1.0',
        'X-Custom-Header' => 'valor'
    ]
);
```

### Crear un Pago Directo

Efectúa un pago directo con tarjeta de crédito, los pagos directos son inmediatamente debitados del cliente y entran en el proceso bancario necesario para acreditar al beneficiario.

```php
use Instapago\Instapago\Api;
use Instapago\Instapago\Exceptions\InstapagoException;

try {
    $api = new Api('<keyId>', '<publicKeyId>');

    $respuesta = $api->directPayment($paymentData);

    // Procesar respuesta exitosa
    echo "ID del Pago: " . $respuesta['id_pago'];
    echo "Referencia: " . $respuesta['reference'];
    echo "Código: " . $respuesta['code'];

} catch (InstapagoException $e) {
    echo "Error procesando el pago: " . $e->getMessage();
    // Manejar el error específico
}
```

#### Usando DTOs para Pagos Directos

```php
use Instapago\Instapago\DTOs\PaymentRequest;
use Instapago\Instapago\Enums\PaymentType;

// Crear request usando DTO
$paymentRequest = new PaymentRequest(
    amount: 200.00,
    description: 'Pago de producto',
    cardHolder: 'Juan Pérez',
    cardHolderId: '12345678',
    cardNumber: '4111111111111111',
    cvc: '123',
    expiration: '12/2026',
    ip: '192.168.1.1'
);

// Convertir a array y procesar
$respuesta = $api->directPayment($paymentRequest->toArray());
```

### Reservar un Pago

Efectúa una reserva o retención de pago en la tarjeta de crédito del cliente, la reserva diferirá los fondos por un tiempo (3 días máximo segun fuentes extraoficiales), en el plazo en el que los fondos se encuentren diferidos, ni el beneficiario ni el cliente poseen el dinero. El dinero será tramitado al beneficiario una vez completado el pago, o de lo contrario será acreditado al cliente de vuelta si no se completa durante el plazo o si se cancela el pago.

```php
try{
  $api = new Api('<keyId>','<publicKeyId>');

  $respuesta = $api->reservePayment($paymentData);
  // hacer algo con $respuesta
}catch(\Instapago\Exceptions\InstapagoException $e){

  echo "Ocurrió un problema procesando el pago.";
  // manejar el error
}
```

### Completar Pago

Este método permite cobrar fondos previamente retenidos.

* `id`: Identificador único del pago.
* `amount`: Monto por el cual se desea procesar el pago final.

```php
try {
    $api = new Api('<keyId>', '<publicKeyId>');

    $respuesta = $api->continuePayment([
        'id' => 'af614bca-0e2b-4232-bc8c-dbedbdf73b48',
        'amount' => 200.00
    ]);

    echo "Pago completado: " . $respuesta['id_pago'];

} catch (InstapagoException $e) {
    echo "Error completando el pago: " . $e->getMessage();
}
```

#### Usando DTO para Completar Pagos

```php
use Instapago\Instapago\DTOs\CompletePaymentRequest;

$completeRequest = new CompletePaymentRequest(
    id: 'af614bca-0e2b-4232-bc8c-dbedbdf73b48',
    amount: 200.00
);

$respuesta = $api->continuePayment($completeRequest->toArray());
```

### Información de un Pago

Consulta información sobre un pago previamente generado.

```php
try{
  $api = new Api('<keyId>','<publicKeyId>');

  $idPago = 'af614bca-0e2b-4232-bc8c-dbedbdf73b48';

  $respuesta = $api->query($idPago);

}catch(\Instapago\Exceptions\InstapagoException $e){
  // manejar errores
}
```
Devuelve la misma respuesta que los métodos de crear pagos.

### Anular Pago

Este método permite cancelar un pago, haya sido directo o retenido.

```php
try {
    $api = new Api('<keyId>', '<publicKeyId>');

    $idPago = 'af614bca-0e2b-4232-bc8c-dbedbdf73b48';

    $info = $api->cancel($idPago);

    echo "Pago cancelado: " . $info['id_pago'];

} catch (InstapagoException $e) {
    echo "Error cancelando el pago: " . $e->getMessage();
}
```
Devuelve la misma respuesta que los métodos de crear pagos.

## Características Avanzadas

### DTOs (Data Transfer Objects)

La librería incluye DTOs tipados para mejorar la seguridad de tipos y facilitar el desarrollo:

#### PaymentRequest

```php
use Instapago\Instapago\DTOs\PaymentRequest;

// Crear desde array
$paymentRequest = PaymentRequest::fromArray($paymentData);

// Crear directamente
$paymentRequest = new PaymentRequest(
    amount: 200.00,
    description: 'Compra en línea',
    cardHolder: 'Juan Pérez',
    cardHolderId: '12345678',
    cardNumber: '4111111111111111',
    cvc: '123',
    expiration: '12/2026',
    ip: '192.168.1.1'
);

// Convertir a array para la API
$requestData = $paymentRequest->toArray();
```

#### PaymentResponse

```php
use Instapago\Instapago\DTOs\PaymentResponse;

// Crear desde respuesta de API
$response = PaymentResponse::fromArray($apiResponse);

// Acceder a propiedades tipadas
echo $response->code;
echo $response->msgBanco;
echo $response->voucher;
echo $response->idPago;
echo $response->reference;
```

#### CompletePaymentRequest

```php
use Instapago\Instapago\DTOs\CompletePaymentRequest;

$completeRequest = new CompletePaymentRequest(
    id: 'payment-id-here',
    amount: 150.00
);

$api->continuePayment($completeRequest->toArray());
```

### Sistema de Validación

La librería utiliza un sistema de validación basado en estrategias que es extensible:

#### Validaciones Incluidas

```php
use Instapago\Instapago\Validation\PaymentValidationStrategy;
use Instapago\Instapago\Validation\QueryValidationStrategy;
use Instapago\Instapago\Validation\CompletePaymentValidationStrategy;

// Las validaciones se aplican automáticamente según el método:
// - directPayment() y reservePayment() usan PaymentValidationStrategy
// - query() usa QueryValidationStrategy
// - continuePayment() usa CompletePaymentValidationStrategy
```

#### Crear Validaciones Personalizadas

```php
use Instapago\Instapago\Validation\ValidationStrategyInterface;
use Instapago\Instapago\Validation\ValidationRuleBuilder;

class CustomValidationStrategy implements ValidationStrategyInterface
{
    public function validate(array $data): void
    {
        $builder = new ValidationRuleBuilder();

        $builder->required('custom_field')
                ->string()
                ->minLength(5);

        $builder->optional('optional_field')
                ->numeric()
                ->min(0);

        $builder->validate($data);
    }
}
```

### Logging

La librería incluye soporte para logging personalizable:

#### Logger por Defecto

```php
use Instapago\Instapago\Logging\NullLogger;

// Logger que no hace nada (por defecto)
$logger = new NullLogger();
```

#### Logger Personalizado

```php
use Instapago\Instapago\Logging\LoggerInterface;

class CustomLogger implements LoggerInterface
{
    public function log(string $level, string $message, array $context = []): void
    {
        // Tu implementación de logging
        error_log("[$level] $message " . json_encode($context));
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }
}

// Usar logger personalizado
$api = new Api('<keyId>', '<publicKeyId>', null, new CustomLogger());
```

### Testing

La librería incluye tests comprehensivos usando Pest PHP:

#### Ejecutar Tests

```bash
# Todos los tests
composer test

# Tests específicos
./vendor/bin/pest tests/ApiInstapagoTest.php
./vendor/bin/pest tests/ValidationTest.php
./vendor/bin/pest tests/RefactoredFeaturesTest.php

# Con coverage
composer test:coverage
```

#### Escribir Tests Personalizados

```php
use Instapago\Instapago\Api;
use Instapago\Instapago\Http\HttpClientInterface;

test('puede mockear cliente HTTP para testing', function () {
    $mockClient = Mockery::mock(HttpClientInterface::class);

    $mockClient->shouldReceive('post')
               ->once()
               ->andReturn([
                   'code' => 201,
                   'msg_banco' => 'Aprobada',
                   'voucher' => '<html>voucher</html>',
                   'id_pago' => 'test-payment-id',
                   'reference' => 123456
               ]);

    $api = new Api('test-key', 'test-public-key', $mockClient);

    $response = $api->directPayment($paymentData);

    expect($response['code'])->toBe(201);
    expect($response['id_pago'])->toBe('test-payment-id');
});
```

#### Arquitectura de Testing

La nueva arquitectura facilita el testing mediante:

- **Dependency Injection**: Permite mockear dependencias fácilmente
- **Interfaces**: Abstracciones para todos los componentes externos
- **DTOs**: Objetos inmutables para datos consistentes
- **Estrategias**: Validaciones y comportamientos intercambiables
