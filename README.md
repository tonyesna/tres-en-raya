# TRES EN RAYA


Proyecto donde se realiza el popular juego del Tres en Raya programado bajo el FrameWork PHP Laravel y JavaScript, así como con el uso de plantillas Blade para la realiación de una vista sencilla.


### Pre-requisitos 📋

- [Composer](https://getcomposer.org/doc/00-intro.md)
- [php v8.0.0](https://www.php.net/manual/es/)
- [Apache](https://httpd.apache.org/docs/)
- [Laravel 8](https://laravel.com/docs/8.x/installation)
- [Mysql v8.0.21](https://dev.mysql.com/doc/)

### Instalación 🔧

* Clonar el proyecto:

  `git clone https://github.com/tonyesna/tres-en-raya.git`

* Generar vendors

  `composer install`

* Configurar las variables de entorno:

    `copiar el fichero .env.example a .env cp .env.example .env`
* Crear una tabla en tu base de datos 
    `nombre de la base de datos => tresenraya`
* Cambiar en el .env la variables de configuración de BBDD. Estarán con el valor genérico:

  `DB_DATABASE=tresenraya`  
  `DB_USERNAME= su username`  
  `DB_PASSWORD= su password`
* Una vez modificado el .env hay que limpiar la cache  
  `php artisan cache:clear`  
  `php artisan config:cache`
* Generar la base de datos: (schema debe estar creado con los datos del .env del entorno)

  `php artisan migrate`

## Ejecutando los Test ⚙️

Test Unitarios:
`php artisan test`

Test Unitarios sobre un solo test:
`php artisan test --filter {especificTest}`

## Autor ✒️

* **Antonio Espinosa**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
