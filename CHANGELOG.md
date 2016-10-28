# Lista de cambios

> Todos los cambios significativos en la librería serán registrados en éste documento.

<small>Lamento haber olvidado hacer los tags correspondientes :( </small>


### v2.0.0
### Nuevo
* Optimizaciones generales
* Ahora utiliza Guzzle para realizar las consultas al API de instapago

### v1.0.1
### Nuevo
* Optimizaciones generales
* Agregando cambios en el método `continuePayment`
* Agregando más pruebas unitarias :smile:
* El método `cancelPayment` está pendiente de revisón. Se solicita información al personal de instapago. :bug:
* Agregado archivo de configuración de phpunit
* Actualizada versión de phpunit

### v1.0.0
### Nuevo
* Optimizaciones generales 
* Agregada una funcion para el manejo de errores (exceptions).
* Eliminado `fullPayment`
* Optimizado ` __construct` (Else is never necessary and you can simplify the code to work without else.)
* Optimización de consulta usando `curl` (Avoid variables with short names like `$ch`.)
* Actualizada documentación

### v0.5.2
### Nuevo
* Eliminado `fullPayment`
* Optimizado ` __construct` (Else is never necessary and you can simplify the code to work without else.)
* Optimización de consulta usando `curl` (Avoid variables with short names like `$ch`.)
* Actualizada documentación

### v0.5.1
### Nuevo
* Fix `InstapagoException` does not exist :bug:
* Agregada carpeta `Exceptions`
* Fix `namespace` de archivo `Exceptions` :bug:

### v0.5.0
### Nuevo
* Se actualiza estructura de directorio
* El autoload de composer ahora usa `psr-4`
* Actualizado el README con información de instalación con composer
* Actualizado el README con información de como usar sin composer
* Agregado manejo de errores.
* Agregado archivo autoload personalizado
* Se crea estructura para las pruebas unitarias :bug:
* Se agrega una prueba unitara sencilla :bug:

### v0.5.0-alpha
### Nuevo
* Se actualiza estructura de directorio
* El autoload de composer ahora usa `psr-4`
* Actualizado el README con información de instalación con composer
* Actualizado el README con información de como usar sin composer
* Agregado manejo de errores.
* Agregado archivo autoload personalizado
* Se crea estructura para las pruebas unitarias :bug:

### v0.4.0
### Nuevo
* Actualizado el README con información de instalación con composer

### v0.3.2-beta
### Nuevo

* Se re-escribe parte del código en el archivo `Instapago.php` para más detalle ver: https://github.com/abr4xas/php-instapago/pull/2

### v0.3.1-beta
### Nuevo

* Se re-escribe parte del código en el archivo `Instapago.php`
* Se agrega en [packagist](https://packagist.org/packages/instapago/instapago)

### v0.3.0
### Nuevo

* Agregado `fullPayment` en `Instapago.php` y actualizada la `DOCUMENTACION.md`
* Eliminado archivo `composer.json`
* Actualizada `DOCUMENTACION.md` y el archivo `Instapago.php`
* Agregado  `continuePayment` y `cancelayment`.


### v0.2.1
### Nuevo
* Eliminado `get_ip`.
* Agregado `paymentInfo`.
* Eliminado `process`.
* Métodos `__construct`, `get_ip`, `payment`.
* `DOCUMENTACION.md`
