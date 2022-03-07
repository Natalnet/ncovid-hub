## Introduction

The Ncovid Hub is the web system that integrates the work of the Ncovid team in order to provide an interface for data visualization of COVID-19 data and predictions as well as model management. 

## Requirements

This software uses the [Laravel](https://laravel.com) framework and requires PHP 7.3 or above and Composer as package manager. It also requires an SQL Database and a web server (Nginx is strongly recommended).

## Installation

In order to install the dependencies, we start by running the `composer install` command. Then, the following commands are used:
```
php artisan key:generate
php artisan migrate
```

## How to use

The running version of this software is available at [ncovid.natalnet.br](http://ncovid.natalnet.br).

## Disclosure notice

The software provided by this repository is currently a subject of research and is not to be used as an official source of information about COVID-19. 

## License

The Ncovid Hub is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
