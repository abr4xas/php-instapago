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
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/abr4xas/php-instapago/run-tests-pest.yml?style=flat-square)](https://github.com/abr4xas/php-instapago/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/instapago/instapago.svg?style=flat-square)](https://packagist.org/packages/instapago/instapago)

## instalación

Primero, [`composer`](https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md)

Luego:

```
$ composer require instapago/instapago
$ composer dumpautoload -o // opcional
```

### Cómo usar

> **NOTA**: Esta versión requiere **PHP 8.2 o superior** y utiliza las características más modernas del lenguaje para ofrecer mejor rendimiento, seguridad y mantenibilidad.

#### Uso Básico

Ver [DOCUMENTACIÓN](./help/DOCUMENTACION.md)

### Arquitectura Refactorizada

Esta versión ha sido completamente refactorizada siguiendo principios SOLID y patrones de diseño modernos:

#### Características Principales:
- **PHP 8.2+** con readonly classes, named arguments y constructor property promotion
- **Dependency Injection** para mejor testabilidad
- **DTOs** para transferencia de datos tipada
- **Strategy Pattern** para validaciones extensibles
- **Factory Pattern** para creación de clientes HTTP
- **Logging** integrado con interfaces estándar
- **Configuración** externalizada y flexible
- **Manejo de errores** unificado y consistente

#### Nuevos Componentes:
- `InstapagoConfig`: Configuración centralizada
- `PaymentRequest/Response`: DTOs tipados
- `ValidationStrategy`: Validaciones extensibles
- `HttpClientInterface`: Abstracción del cliente HTTP
- `LoggerInterface`: Logging personalizable

### Tests

La librería incluye tests comprehensivos usando Pest PHP:

```bash
# Ejecutar todos los tests
composer test

# Ejecutar tests con coverage
composer test:coverage

```

#### Estadísticas de Tests:
- **40 tests** exitosos
- **128 assertions** cubriendo todas las funcionalidades
- **Cobertura completa** de métodos públicos y casos edge

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Angel Cruz](https://github.com/abr4xas)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
