# Datos da COVID-19 en Concellos de Galicia

Este proxecto contén a evolución de casos da COVID-19 nos concellos de Galicia.


## Recoñecementos

- Baseado na recompilación de datos de https://github.com/lipido/galicia-covid19


## Requerimentos

Este proxecto precisa:

* PHP 7.4
* [Composer]


## Instalación e uso

Para instalar a aplicación e inicializar a [base de datos][bbdd] simplemente usaremos a típica orde
de instalación de [Composer]:

```bash
composer install
```

Para executar a aplicación podemos usar o servidor interno de PHP:

```bash
php -S localhost:8080 -t public
```

De xeito alternativo tamén podes usar, por exemplo, o servidor de Symfony:


```bash
symfony server:start
```

### Actualización de datos

Este proxecto usa os datos proporcionados polo repositorio [lipido/galicia-covid19][bbdd], que se actualiza de forma diaria. Para obter os cambios e manter actualizada a copia, executa o seguinte:

```bash
php bin\console app:build-data
```


[Composer]: <https://getcomposer.org> "Composer - A Dependency Manager for PHP"
[bbdd]: <https://github.com/lipido/galicia-covid19> "Datos COVID-19 Galicia"
