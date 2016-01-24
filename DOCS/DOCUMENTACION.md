<p align="center">
    <img alt="node-instapago" src="http://i.imgur.com/hYNsH6B.jpg" width="auto">
</p>
<p align="center">
    Documentación de la librería <b>Instapago</b>
</p>

----

## tabla de contenido

* [instalación](#instalación)
* [uso de la librería](#uso-de-la-librería)
	* [crear un pago](#crear-un-pago)
        * [parámetros requeridos para crear el pago](#parámetros-requeridos-para-crear-el-pago)
        * [parámetros opcionales para crear el pago](#parámetros-opcionales-para-crear-el-pago)
        * [ejemplo](#ejemplo)
* [códigos de respuesta](#códigos-de-respuesta)
* [licencia](#licencia)

## instalación

```bash
// TODO
```

## uso de la librería

### implementación

```php
$api = new Instapago('<keyId>','<publicKeyId>');
```

### llaves de pruebas

```
* keyId = 74D4A278-C3F8-4D7A-9894-FA0571D7E023
* publicKeyId = e9a5893e047b645fed12c82db877e05a
```
> **Importante**: Se debe solicitar las llaves `keyId` y `publicKeyId` en la página de Instapago. [Aquí](http://instapago.com/wp-content/uploads/2015/10/Guia-Integracion-API-Instapago-1.6.pdf) puedes encontrar mayor información.

## crear un pago

Efectúa un pago con tarjeta de crédito, una vez procesado retornar una respuesta.

```php
$pago = $api->payment(200,"test","jon doe",11111111,4111111111111111,123,"02/2016","2");
```
#### Parámetros _requeridos_ para crear el pago

* `amount` Monto a Debitar, utilizando punto (.) como separador decimal. Por ejemplo: 200.00
* `description` Texto con la descripción de la operación.
* `card_holder` Nombre del Tarjeta habiente.
* `card_holder_id` Cédula o RIF del Tarjeta habiente.
* `card_number` Número de la tarjeta de crédito, sin espacios ni
separadores.
* `cvc` Código secreto de la Tarjeta de crédito.
* `expiration_date` Fecha de expiración de la tarjeta en el formato mostrado
en la misma MM/YYYY. Por Ejemplo: 10/2015.
* `status_id` Status en el que se creará la transacción.
    * 1: Retener (Pre-Autorización).
    * 2: Pagar (Autorización).
* `ip` Dirección IP del cliente.

#### Parámetros _opcionales_ para crear el pago

// TODO

### ejemplo

```php
$api = new Instapago('<keyId>','<publicKeyId>');

$pago = $api->payment("200","test","jon doe","11111111","4111111111111111","123","02/2016","2");

echo '
	Mensaje del banco: <strong>'.$pago['msg_banco'].'</strong> </br> 
	Voucher</br>'.$pago['voucher'] .'</br> 
	Identificador del pago</br><strong>'. $pago['id_pago'] .'</strong></br> 
	Código de referencia: ' . '<strong>' . $pago['reference'] .'</strong>';
```

### códigos de respuesta

Para todas las transacciones realizadas bajo el API de Instapago, los códigos HTTP de respuestas corresponden a los siguientes estados:

* ```201```: Pago procesado con éxito.
* ```400```: Error al validar los datos enviados (Adicionalmente se devuelve una cadena de
caracteres con la descripción del error).
* ```401```: Error de autenticación, ha ocurrido un error con las llaves utilizadas.
* ```403```: Pago Rechazado por el banco.
* ```500```: Ha Ocurrido un error interno dentro del servidor.
* ```503```: Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos
enviados y vuelva a intentarlo.

> **Importante**: Si recibe un código de respuesta diferente a los antes descritos deben ser tomados como errores de protocolo HTTP.

# licencia

Licencia [MIT](http://opensource.org/licenses/MIT) :copyright: 2015 [Autores de la librería](AUTORES.md)